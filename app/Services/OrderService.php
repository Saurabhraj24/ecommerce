<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\StockLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected RazorpayService $razorpayService
    ) {}

    /**
     * Create an order from the user's/session's cart.
     *
     * @param Cart $cart
     * @param array $customerDetails
     * @param array $shippingAddress
     * @param string $paymentMethod
     * @param string|null $notes
     * @return Order
     * @throws Exception
     */
    public function createOrderFromCart(
        Cart $cart,
        array $customerDetails,
        array $shippingAddress,
        string $paymentMethod = 'cod',
        ?string $notes = null
    ): Order {
        if ($cart->items->isEmpty()) {
            throw new Exception('Your cart is empty.');
        }

        // Validate stock for all items
        foreach ($cart->items as $item) {
            $product = $item->product;
            if (!$product || !$product->is_active) {
                throw new Exception("Product '{$product?->name}' is currently unavailable.");
            }

            if ($item->variant) {
                if ($item->variant->stock < $item->quantity) {
                    throw new Exception("Only {$item->variant->stock} left in stock for {$product->name} ({$item->variant->label}).");
                }
            } else {
                if ($product->stock < $item->quantity) {
                    throw new Exception("Only {$product->stock} left in stock for {$product->name}.");
                }
            }
        }

        // Validate COD settings if COD selected
        if ($paymentMethod === 'cod') {
            $codEnabled = Setting::get('cod_enabled', true);
            if (!$codEnabled) {
                throw new Exception('Cash on Delivery is currently disabled.');
            }

            $codMaxLimit = (float) Setting::get('cod_max_limit', 10000.0);
            if ($cart->grand_total > $codMaxLimit) {
                throw new Exception("Cash on Delivery is only available for orders up to ₹" . number_format($codMaxLimit, 2));
            }
        }

        return DB::transaction(function () use ($cart, $customerDetails, $shippingAddress, $paymentMethod, $notes) {
            $orderNumber = Order::generateOrderNumber();

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $cart->user_id,
                'customer_name' => $customerDetails['name'],
                'customer_email' => $customerDetails['email'],
                'customer_phone' => $customerDetails['phone'],
                'shipping_address' => $shippingAddress,
                'subtotal' => $cart->subtotal,
                'discount_amount' => $cart->discount_amount,
                'tax_amount' => $cart->tax_amount,
                'shipping_charge' => $cart->shipping_charge,
                'grand_total' => $cart->grand_total,
                'coupon_code' => $cart->coupon?->code,
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'order_status' => ($paymentMethod === 'cod') ? 'confirmed' : 'pending',
                'notes' => $notes,
            ]);

            // Create order items & adjust inventory
            foreach ($cart->items as $item) {
                $variantDetails = $item->variant ? $item->variant->label : null;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->variant?->sku ?? $item->product->sku,
                    'variant_details' => $variantDetails,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'total_price' => $item->subtotal,
                ]);

                // Reduce stock
                $this->reduceStock($item->product, $item->variant, $item->quantity, $order);
            }

            // Increment coupon usage
            if ($cart->coupon) {
                $cart->coupon->increment('used_count');
            }

            // Create initial payment record
            if ($paymentMethod === 'cod') {
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => 'cod',
                    'amount' => $order->grand_total,
                    'currency' => 'INR',
                    'status' => 'pending',
                ]);

                // Clear cart immediately for COD
                $cart->items()->delete();
                $cart->update(['coupon_id' => null]);
            }

            ActivityLog::log('order_created', Order::class, $order->id, [
                'order_number' => $order->order_number,
                'grand_total' => $order->grand_total,
                'payment_method' => $paymentMethod,
            ]);

            return $order;
        });
    }

    /**
     * Deduct stock and write a log entry.
     */
    protected function reduceStock(Product $product, ?ProductVariant $variant, int $quantity, Order $order): void
    {
        if ($variant) {
            $old = $variant->stock;
            $new = max(0, $old - $quantity);
            $variant->update(['stock' => $new]);

            StockLog::create([
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'user_id' => $order->user_id,
                'type' => 'order_placed',
                'quantity_change' => -$quantity,
                'old_quantity' => $old,
                'new_quantity' => $new,
                'reason' => "Order #{$order->order_number}",
            ]);
        } else {
            $old = $product->stock;
            $new = max(0, $old - $quantity);
            $product->update(['stock' => $new]);

            StockLog::create([
                'product_id' => $product->id,
                'product_variant_id' => null,
                'user_id' => $order->user_id,
                'type' => 'order_placed',
                'quantity_change' => -$quantity,
                'old_quantity' => $old,
                'new_quantity' => $new,
                'reason' => "Order #{$order->order_number}",
            ]);
        }
    }

    /**
     * Restock order items when cancelled or returned.
     */
    public function restockOrder(Order $order, string $type = 'order_cancelled', string $reason = 'Order cancelled'): void
    {
        foreach ($order->items as $item) {
            if ($item->product_variant_id && $item->variant) {
                $old = $item->variant->stock;
                $new = $old + $item->quantity;
                $item->variant->update(['stock' => $new]);

                StockLog::create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'user_id' => auth()->id() ?? $order->user_id,
                    'type' => $type,
                    'quantity_change' => $item->quantity,
                    'old_quantity' => $old,
                    'new_quantity' => $new,
                    'reason' => "{$reason} #{$order->order_number}",
                ]);
            } elseif ($item->product) {
                $old = $item->product->stock;
                $new = $old + $item->quantity;
                $item->product->update(['stock' => $new]);

                StockLog::create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => null,
                    'user_id' => auth()->id() ?? $order->user_id,
                    'type' => $type,
                    'quantity_change' => $item->quantity,
                    'old_quantity' => $old,
                    'new_quantity' => $new,
                    'reason' => "{$reason} #{$order->order_number}",
                ]);
            }
        }
    }

    /**
     * Mark an online Razorpay payment as successful.
     */
    public function markPaymentSuccessful(Order $order, string $razorpayPaymentId, string $razorpayOrderId, ?string $signature = null, ?array $payload = null): void
    {
        DB::transaction(function () use ($order, $razorpayPaymentId, $razorpayOrderId, $signature, $payload) {
            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_method' => 'razorpay',
                    'razorpay_order_id' => $razorpayOrderId,
                    'razorpay_payment_id' => $razorpayPaymentId,
                    'razorpay_signature' => $signature,
                    'amount' => $order->grand_total,
                    'currency' => 'INR',
                    'status' => 'successful',
                    'response_payload' => $payload,
                ]
            );

            $order->update([
                'payment_status' => 'paid',
                'order_status' => 'confirmed',
            ]);

            ActivityLog::log('payment_received', Order::class, $order->id, [
                'order_number' => $order->order_number,
                'payment_id' => $razorpayPaymentId,
                'amount' => $order->grand_total,
            ]);
        });
    }
}
