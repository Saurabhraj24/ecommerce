<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\Setting;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\RazorpayService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected OrderService $orderService,
        protected RazorpayService $razorpayService
    ) {}

    public function index(): View|RedirectResponse
    {
        $cart = $this->cartService->getCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Please add items before checking out.');
        }

        $addresses = Auth::check() ? Auth::user()->addresses : collect();
        $defaultAddress = Auth::check() ? Auth::user()->defaultAddress : null;

        $codEnabled = Setting::get('cod_enabled', true);
        $codMaxLimit = (float) Setting::get('cod_max_limit', 10000.0);
        $razorpayEnabled = Setting::get('razorpay_enabled', true);

        $summary = $this->cartService->calculateSummary($cart);
        $codAllowed = $codEnabled;
        $codLimit = $codMaxLimit;
        $razorpayAllowed = $razorpayEnabled;

        return view('checkout.index', compact(
            'cart',
            'summary',
            'addresses',
            'defaultAddress',
            'codEnabled',
            'codMaxLimit',
            'razorpayEnabled',
            'codAllowed',
            'codLimit',
            'razorpayAllowed'
        ));
    }

    public function process(Request $request): View|RedirectResponse
    {
        $cart = $this->cartService->getCart();

        // If cart is empty, check if an order was just placed in the last 60 seconds (prevents double-click duplicate error)
        if ($cart->items->isEmpty()) {
            $recentOrder = Order::where(function ($q) use ($request) {
                if (Auth::check()) {
                    $q->where('user_id', Auth::id());
                } else {
                    $phone = $request->input('customer_phone') ?? $request->input('phone');
                    $email = $request->input('customer_email') ?? $request->input('email');
                    if ($phone) $q->where('customer_phone', $phone);
                    if ($email) $q->orWhere('customer_email', $email);
                }
            })->where('created_at', '>=', now()->subSeconds(60))
              ->latest()
              ->first();

            if ($recentOrder) {
                return redirect()->route('checkout.success', $recentOrder->order_number)
                    ->with('success', 'Order placed successfully! We will prepare your package shortly.');
            }

            return redirect()->route('cart.index')->with('error', 'Your shopping bag is empty.');
        }

        // Concurrency lock to prevent simultaneous double-clicks from creating parallel duplicate orders
        $lockKey = 'checkout_lock_' . (Auth::id() ?? session()->getId());
        $lock = \Illuminate\Support\Facades\Cache::lock($lockKey, 15);

        if (!$lock->get()) {
            // Concurrent click detected: wait briefly and redirect to completed order
            sleep(2);
            $recentOrder = Order::where(function ($q) {
                if (Auth::check()) {
                    $q->where('user_id', Auth::id());
                }
            })->where('created_at', '>=', now()->subSeconds(30))
              ->latest()
              ->first();

            if ($recentOrder) {
                return redirect()->route('checkout.success', $recentOrder->order_number)
                    ->with('success', 'Order placed successfully!');
            }

            return redirect()->route('cart.index')->with('info', 'Your order is currently processing.');
        }

        // Map form input aliases
        $request->merge([
            'saved_address_id' => $request->input('saved_address_id') ?? $request->input('shipping_address_id'),
            'customer_name' => $request->input('customer_name') ?? $request->input('name') ?? (Auth::user()?->name),
            'customer_email' => $request->input('customer_email') ?? $request->input('email') ?? (Auth::user()?->email),
            'customer_phone' => $request->input('customer_phone') ?? $request->input('phone') ?? (Auth::user()?->phone),
            'postal_code' => $request->input('postal_code') ?? $request->input('pincode'),
            'order_notes' => $request->input('order_notes') ?? $request->input('customer_notes'),
        ]);

        // Validate checkout inputs
        $validated = $request->validate([
            'saved_address_id' => 'nullable|exists:addresses,id',
            'customer_name' => 'required_without:saved_address_id|string|max:255',
            'customer_email' => 'required_without:saved_address_id|email|max:255',
            'customer_phone' => 'required_without:saved_address_id|string|regex:/^[0-9+\s\-]{10,15}$/',
            'address_line1' => 'required_without:saved_address_id|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'city' => 'required_without:saved_address_id|string|max:100',
            'state' => 'required_without:saved_address_id|string|max:100',
            'postal_code' => 'required_without:saved_address_id|string|regex:/^[0-9]{6}$/',
            'payment_method' => 'required|in:cod,razorpay',
            'save_address' => 'nullable|boolean',
            'order_notes' => 'nullable|string|max:500',
        ]);

        try {
            $shippingAddress = [];
            $customerDetails = [];

            if (!empty($validated['saved_address_id']) && Auth::check()) {
                $address = Address::where('user_id', Auth::id())->findOrFail($validated['saved_address_id']);
                $customerDetails = [
                    'name' => $address->full_name,
                    'email' => Auth::user()->email,
                    'phone' => $address->phone,
                ];
                $shippingAddress = [
                    'full_name' => $address->full_name,
                    'phone' => $address->phone,
                    'alternate_phone' => $address->alternate_phone,
                    'address_line1' => $address->address_line1,
                    'address_line2' => $address->address_line2,
                    'landmark' => $address->landmark,
                    'city' => $address->city,
                    'state' => $address->state,
                    'postal_code' => $address->postal_code,
                    'address_type' => $address->address_type,
                ];
            } else {
                $customerDetails = [
                    'name' => $validated['customer_name'],
                    'email' => $validated['customer_email'],
                    'phone' => $validated['customer_phone'],
                ];
                $shippingAddress = [
                    'full_name' => $validated['customer_name'],
                    'phone' => $validated['customer_phone'],
                    'address_line1' => $validated['address_line1'],
                    'address_line2' => $validated['address_line2'] ?? null,
                    'landmark' => $validated['landmark'] ?? null,
                    'city' => $validated['city'],
                    'state' => $validated['state'],
                    'postal_code' => $validated['postal_code'],
                    'address_type' => 'home',
                ];

                // If user opted to save address
                if (Auth::check() && !empty($validated['save_address'])) {
                    Address::create([
                        'user_id' => Auth::id(),
                        'full_name' => $validated['customer_name'],
                        'phone' => $validated['customer_phone'],
                        'address_line1' => $validated['address_line1'],
                        'address_line2' => $validated['address_line2'] ?? null,
                        'landmark' => $validated['landmark'] ?? null,
                        'city' => $validated['city'],
                        'state' => $validated['state'],
                        'postal_code' => $validated['postal_code'],
                        'is_default' => (Auth::user()->addresses()->count() === 0),
                    ]);
                }
            }

            // Idempotency check: verify if an identical order was already created in the last 15 seconds
            $recentDuplicate = Order::where(function ($q) use ($customerDetails) {
                if (Auth::check()) {
                    $q->where('user_id', Auth::id());
                } else {
                    $q->where('customer_phone', $customerDetails['phone'])
                      ->orWhere('customer_email', $customerDetails['email']);
                }
            })->where('created_at', '>=', now()->subSeconds(15))
              ->latest()
              ->first();

            if ($recentDuplicate && $cart->items->isEmpty()) {
                return redirect()->route('checkout.success', $recentDuplicate->order_number)
                    ->with('success', 'Order placed successfully! We will prepare your package shortly.');
            }

            // Create Order
            $order = $this->orderService->createOrderFromCart(
                $cart,
                $customerDetails,
                $shippingAddress,
                $validated['payment_method'],
                $validated['order_notes'] ?? null
            );

            // Handle COD directly
            if ($order->payment_method === 'cod') {
                return redirect()->route('checkout.success', $order->order_number)
                    ->with('success', 'Order placed successfully! We will prepare your package shortly.');
            }

            // Handle Razorpay Online Payment
            $razorpayOrder = $this->razorpayService->createOrder($order);

            return view('checkout.razorpay', [
                'order' => $order,
                'razorpayOrder' => $razorpayOrder,
                'razorpayKey' => $this->razorpayService->getKey(),
            ]);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } finally {
            if (isset($lock)) {
                $lock->release();
            }
        }
    }

    public function verifyPayment(Request $request): RedirectResponse
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $order = Order::findOrFail($request->order_id);

        $isValid = $this->razorpayService->verifySignature(
            $request->razorpay_order_id,
            $request->razorpay_payment_id,
            $request->razorpay_signature
        );

        if (!$isValid) {
            $order->update(['payment_status' => 'failed']);
            return redirect()->route('checkout.index')->with('error', 'Payment verification failed. Please try again.');
        }

        // Mark successful
        $this->orderService->markPaymentSuccessful(
            $order,
            $request->razorpay_payment_id,
            $request->razorpay_order_id,
            $request->razorpay_signature,
            $request->all()
        );

        // Clear user cart
        $cart = $this->cartService->getCart();
        $cart->items()->delete();
        $cart->update(['coupon_id' => null]);

        return redirect()->route('checkout.success', $order->order_number)
            ->with('success', 'Payment verified successfully! Thank you for ordering from Bombay Fashion.');
    }

    public function success(string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product', 'items.variant'])
            ->firstOrFail();

        return view('checkout.success', compact('order'));
    }

    public function webhook(Request $request): JsonResponse
    {
        $signature = $request->header('X-Razorpay-Signature', '');
        $payload = $request->getContent();

        if (!$this->razorpayService->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Invalid Razorpay webhook signature');
            return response()->json(['status' => 'invalid_signature'], 400);
        }

        $data = json_decode($payload, true);
        $event = $data['event'] ?? '';

        if ($event === 'payment.captured') {
            $paymentEntity = $data['payload']['payment']['entity'] ?? [];
            $razorpayOrderId = $paymentEntity['order_id'] ?? null;
            $paymentId = $paymentEntity['id'] ?? null;

            if ($razorpayOrderId) {
                $payment = \App\Models\Payment::where('razorpay_order_id', $razorpayOrderId)->first();
                if ($payment && $payment->order) {
                    $this->orderService->markPaymentSuccessful(
                        $payment->order,
                        $paymentId,
                        $razorpayOrderId,
                        null,
                        $data
                    );
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
