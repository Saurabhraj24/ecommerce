<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get or create the current active cart.
     */
    public function getCart(): Cart
    {
        $sessionId = Session::getId();

        if (Auth::check()) {
            $user = Auth::user();
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            // Check if there was a guest cart for this session to merge
            if ($sessionId) {
                $cart->mergeGuestCart($sessionId);
            }

            return $cart->load(['items.product.primaryImage', 'items.product.images', 'items.variant', 'coupon']);
        }

        $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        return $cart->load(['items.product.primaryImage', 'items.product.images', 'items.variant', 'coupon']);
    }

    /**
     * Add a product/variant to the cart.
     */
    public function addItem(int $productId, ?int $variantId = null, int $quantity = 1): CartItem
    {
        $product = Product::active()->findOrFail($productId);
        $cart = $this->getCart();

        $variant = null;
        if ($variantId) {
            $variant = ProductVariant::where('product_id', $productId)
                ->where('is_active', true)
                ->findOrFail($variantId);
        }

        // Available stock
        $availableStock = $variant ? $variant->stock : $product->stock;
        if ($availableStock < 1) {
            throw new Exception('Selected item is currently out of stock.');
        }

        // Check existing item in cart
        $cartItem = $cart->items()
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();

        $newQty = $cartItem ? ($cartItem->quantity + $quantity) : $quantity;

        if ($newQty > $availableStock) {
            throw new Exception("Only {$availableStock} items are available in stock.");
        }

        $unitPrice = $variant ? $variant->effective_price : $product->effective_price;

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $newQty,
                'unit_price' => $unitPrice,
            ]);
            return $cartItem;
        }

        return $cart->items()->create([
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
        ]);
    }

    /**
     * Update quantity for a cart item.
     */
    public function updateItemQuantity(int $cartItemId, int $quantity): void
    {
        $cart = $this->getCart();
        $item = $cart->items()->findOrFail($cartItemId);

        if ($quantity <= 0) {
            $item->delete();
            return;
        }

        $availableStock = $item->variant ? $item->variant->stock : $item->product->stock;
        if ($quantity > $availableStock) {
            throw new Exception("Only {$availableStock} items are available in stock.");
        }

        $item->update(['quantity' => $quantity]);
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(int $cartItemId): void
    {
        $cart = $this->getCart();
        $cart->items()->where('id', $cartItemId)->delete();
    }

    /**
     * Apply a coupon code to the cart.
     */
    public function applyCoupon(string $code): Coupon
    {
        $cart = $this->getCart();
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        if (!$coupon) {
            throw new Exception('Invalid coupon code.');
        }

        $errorMsg = null;
        if (!$coupon->isValidForAmount($cart->subtotal, $errorMsg)) {
            throw new Exception($errorMsg ?? 'This coupon cannot be applied.');
        }

        $cart->update(['coupon_id' => $coupon->id]);
        return $coupon;
    }

    /**
     * Remove applied coupon.
     */
    public function removeCoupon(): void
    {
        $cart = $this->getCart();
        $cart->update(['coupon_id' => null]);
    }

    /**
     * Calculate summary dictionary for views.
     */
    public function calculateSummary(?Cart $cart = null): array
    {
        $cart = $cart ?? $this->getCart();
        return [
            'subtotal' => $cart->subtotal,
            'discount' => $cart->discount_amount,
            'coupon' => $cart->coupon ? ['code' => $cart->coupon->code, 'discount' => $cart->discount_amount] : null,
            'tax_rate' => (float) \App\Models\Setting::get('tax_rate', 5.0),
            'tax' => $cart->tax_amount,
            'shipping' => $cart->shipping_charge,
            'free_shipping_threshold' => (float) \App\Models\Setting::get('free_shipping_threshold', 999.0),
            'total' => $cart->grand_total,
            'item_count' => $cart->item_count,
        ];
    }
}
