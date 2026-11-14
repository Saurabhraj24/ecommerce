<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PosController extends Controller
{
    /**
     * Display POS Counter Interface.
     */
    public function index(Request $request): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $query = Product::where('is_active', true)
            ->with(['primaryImage', 'category', 'variants' => function ($q) {
                $q->where('is_active', true)->orderBy('size');
            }])
            ->orderBy('name');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('q')) {
            $search = trim($request->string('q'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhereHas('variants', function ($vq) use ($search) {
                      $vq->where('sku', 'like', "%{$search}%");
                  });
            });
        }

        $products = $query->paginate(24)->withQueryString();

        // Recent counter orders for quick lookup
        $recentPosOrders = Order::where(function ($q) {
                $q->where('notes', 'like', '%POS%')
                  ->orWhere('shipping_address', 'like', '%Showroom Counter%')
                  ->orWhere('order_number', 'like', 'BF-POS-%');
            })
            ->latest()
            ->take(8)
            ->get();
        // Staff members for staff/self-purchase quick selection
        $staffRole = Role::where('name', Role::STAFF)->first();
        $adminRole = Role::where('name', Role::ADMIN)->first();
        $staffMembers = User::whereIn('role_id', array_filter([$staffRole?->id, $adminRole?->id]))
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone', 'employee_id']);

        return view('admin.pos.index', compact('products', 'categories', 'recentPosOrders', 'staffMembers'));
    }

    /**
     * Process POS Counter Checkout.
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'staff_user_id' => 'nullable|integer|exists:users,id',
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:100',
            'payment_method' => 'required|in:cash,upi,card',
            'discount_amount' => 'nullable|numeric|min:0',
            'cash_tendered' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.variant_id' => 'required|integer|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $staffUserId = $validated['staff_user_id'] ?? null;
        $customerName = trim($validated['customer_name'] ?? '') ?: 'Showroom Walk-in Customer';
        $customerPhone = trim($validated['customer_phone'] ?? '') ?: 'Walk-in';
        $customerEmail = trim($validated['customer_email'] ?? '') ?: null;
        $paymentMethod = $validated['payment_method'];
        $discountAmount = max(0, (float)($validated['discount_amount'] ?? 0));
        $cashTendered = (float)($validated['cash_tendered'] ?? 0);

        // Identify buyer if staff self-purchase or registered customer
        $buyerUser = null;
        if (!empty($staffUserId)) {
            $buyerUser = User::find($staffUserId);
        } elseif (!empty($customerPhone) && $customerPhone !== 'Walk-in') {
            $cleanPhone = preg_replace('/[^0-9]/', '', $customerPhone);
            $buyerUser = User::where('phone', $customerPhone)
                ->orWhere('phone', 'like', "%{$cleanPhone}%")
                ->first();
        } elseif (!empty($customerEmail)) {
            $buyerUser = User::where('email', $customerEmail)->first();
        }

        $buyerUserId = $buyerUser?->id;
        if ($buyerUser) {
            $customerName = $buyerUser->name;
            $customerEmail = $buyerUser->email;
            $customerPhone = $buyerUser->phone ?: $customerPhone;
        } else {
            $customerEmail = $customerEmail ?: 'walkin@bombayfashion.com';
        }

        try {
            $order = DB::transaction(function () use (
                $validated,
                $buyerUserId,
                $customerName,
                $customerEmail,
                $customerPhone,
                $paymentMethod,
                $discountAmount,
                $cashTendered
            ) {
                $subtotal = 0;
                $orderItemsData = [];
                $stockDeductions = [];

                // 1. Validate all items and live stock
                foreach ($validated['items'] as $item) {
                    $variant = ProductVariant::with('product')->lockForUpdate()->findOrFail($item['variant_id']);
                    $qty = (int)$item['quantity'];

                    if ($variant->stock < $qty) {
                        throw new \Exception("Insufficient stock for {$variant->product->name} (Size: {$variant->size}). Available: {$variant->stock}, requested: {$qty}.");
                    }

                    $unitPrice = (float)($variant->effective_price ?? (($variant->product->discount_price ?? $variant->product->price) + $variant->additional_price));
                    $itemTotal = $unitPrice * $qty;
                    $subtotal += $itemTotal;

                    $variantDetails = trim(($variant->size ?? '') . ' ' . ($variant->color ?? ''));

                    $orderItemsData[] = [
                        'variant' => $variant,
                        'product_id' => $variant->product_id,
                        'product_variant_id' => $variant->id,
                        'product_name' => $variant->product->name,
                        'product_sku' => $variant->sku ?? $variant->product->sku,
                        'variant_details' => $variantDetails ?: 'Standard',
                        'unit_price' => $unitPrice,
                        'quantity' => $qty,
                        'total_price' => $itemTotal,
                    ];

                    $stockDeductions[] = [
                        'variant' => $variant,
                        'quantity' => $qty,
                    ];
                }

                // 2. Calculate Totals (5% GST standard apparel)
                $discount = min($discountAmount, $subtotal);
                $taxableAmount = max(0, $subtotal - $discount);
                $taxAmount = round($taxableAmount * 0.05, 2);
                $grandTotal = round($taxableAmount + $taxAmount, 2);

                $changeDue = $paymentMethod === 'cash' && $cashTendered > $grandTotal
                    ? round($cashTendered - $grandTotal, 2)
                    : 0;

                // 3. Generate Order Number
                $orderNumber = 'BF-POS-' . date('Ymd') . '-' . strtoupper(Str::random(4));

                $cashier = Auth::user();
                $cashierRoleName = $cashier->role?->name ?? ($cashier->isAdmin() ? 'Admin' : 'Staff');

                // 4. Create Order
                $order = Order::create([
                    'order_number' => $orderNumber,
                    'user_id' => $buyerUserId,
                    'billed_by_id' => $cashier->id,
                    'cashier_name' => $cashier->name,
                    'cashier_email' => $cashier->email,
                    'cashier_role' => $cashierRoleName,
                    'customer_name' => $customerName,
                    'customer_email' => $customerEmail,
                    'customer_phone' => $customerPhone,
                    'shipping_address' => [
                        'name' => $customerName,
                        'phone' => $customerPhone,
                        'address_line1' => 'Bombay Fashion Amritsar Showroom Counter',
                        'city' => 'Amritsar',
                        'state' => 'Punjab',
                        'pincode' => '143001',
                    ],
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount,
                    'tax_amount' => $taxAmount,
                    'shipping_charge' => 0,
                    'grand_total' => $grandTotal,
                    'coupon_code' => $discount > 0 ? 'COUNTER-DISC' : null,
                    'payment_method' => $paymentMethod,
                    'payment_status' => 'paid',
                    'order_status' => 'delivered',
                    'notes' => "POS Walk-in Counter Sale (Billed by {$cashier->name} - {$cashier->email})",
                    'delivered_at' => now(),
                ]);

                // 5. Create Order Items and update inventory
                foreach ($orderItemsData as $itemData) {
                    $variant = $itemData['variant'];
                    unset($itemData['variant']);
                    $itemData['order_id'] = $order->id;
                    OrderItem::create($itemData);

                    // Deduct variant stock
                    $oldStock = $variant->stock;
                    $variant->decrement('stock', $itemData['quantity']);
                    $newStock = $variant->stock;

                    // Deduct parent product stock
                    $product = $variant->product;
                    $product->decrement('stock', $itemData['quantity']);

                    // Log in StockLog audit trail
                    StockLog::create([
                        'product_id' => $variant->product_id,
                        'product_variant_id' => $variant->id,
                        'user_id' => Auth::id(),
                        'type' => 'order_placed',
                        'quantity_change' => -$itemData['quantity'],
                        'old_quantity' => $oldStock,
                        'new_quantity' => $newStock,
                        'reason' => "POS Walk-in Counter Sale #{$order->order_number}",
                    ]);
                }

                // 6. Record Payment
                Payment::create([
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'payment_method' => $paymentMethod,
                    'transaction_id' => 'POS-' . strtoupper(Str::random(10)),
                    'amount' => $grandTotal,
                    'currency' => 'INR',
                    'status' => 'successful',
                    'payment_details' => [
                        'cash_tendered' => $cashTendered,
                        'change_due' => $changeDue,
                        'counter_cashier' => Auth::user()->name,
                        'timestamp' => now()->toIso8601String(),
                    ],
                ]);

                return $order;
            });

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully! Inventory deducted.',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'grand_total' => number_format((float)$order->grand_total, 2),
                'receipt_url' => route('admin.pos.receipt', $order->id),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Printable Thermal Showroom Receipt.
     */
    public function receipt(Order $order): View
    {
        $user = Auth::user();
        $isBuyer = ($order->user_id && $order->user_id === $user?->id) || ($order->customer_email && $order->customer_email === $user?->email);
        $isBiller = $order->billed_by_id && $order->billed_by_id === $user?->id;

        if (!$user?->isAdmin() && !$user?->hasPermission('pos.access') && !$isBuyer && !$isBiller) {
            abort(403, 'Unauthorized to view this POS receipt.');
        }

        $order->load(['items.product', 'items.variant']);

        return view('admin.pos.receipt', compact('order'));
    }

    /**
     * Showroom Daily Counter Register Sessions & History.
     */
    public function history(Request $request): View
    {
        $date = $request->input('date', now()->toDateString());

        $ordersQuery = Order::where(function ($q) {
                $q->where('notes', 'like', '%POS%')
                  ->orWhere('shipping_address', 'like', '%Showroom Counter%')
                  ->orWhere('order_number', 'like', 'BF-POS-%');
            })
            ->whereDate('created_at', $date);

        $totalSales = (clone $ordersQuery)->where('payment_status', 'paid')->sum('grand_total');
        $totalOrders = (clone $ordersQuery)->count();
        $cashSales = (clone $ordersQuery)->where('payment_method', 'cash')->where('payment_status', 'paid')->sum('grand_total');
        $upiSales = (clone $ordersQuery)->whereIn('payment_method', ['upi', 'razorpay'])->where('payment_status', 'paid')->sum('grand_total');
        $cardSales = (clone $ordersQuery)->where('payment_method', 'card')->where('payment_status', 'paid')->sum('grand_total');

        $orders = (clone $ordersQuery)->with(['items', 'user'])->latest()->paginate(20)->withQueryString();

        return view('admin.pos.history', compact('orders', 'date', 'totalSales', 'totalOrders', 'cashSales', 'upiSales', 'cardSales'));
    }
}
