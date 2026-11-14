<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'coupon_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class)->with(['product.primaryImage', 'product.images', 'variant']);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(fn(CartItem $item) => $item->subtotal);
    }

    public function getDiscountAmountAttribute(): float
    {
        if (!$this->coupon) {
            return 0.00;
        }

        if (!$this->coupon->isValidForAmount($this->subtotal)) {
            return 0.00;
        }

        return $this->coupon->calculateDiscount($this->subtotal);
    }

    public function getTaxAmountAttribute(): float
    {
        $taxPercent = (float) Setting::get('tax_rate', 5.0);
        $taxable = max(0, $this->subtotal - $this->discount_amount);
        return round(($taxable * $taxPercent) / 100, 2);
    }

    public function getShippingChargeAttribute(): float
    {
        $freeShippingThreshold = (float) Setting::get('free_shipping_threshold', 999.0);
        $flatShipping = (float) Setting::get('shipping_charge', 50.0);

        if ($this->subtotal >= $freeShippingThreshold || $this->items->isEmpty()) {
            return 0.00;
        }

        return $flatShipping;
    }

    public function getGrandTotalAttribute(): float
    {
        return max(0, round($this->subtotal - $this->discount_amount + $this->tax_amount + $this->shipping_charge, 2));
    }

    public function getItemCountAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }

    public function mergeGuestCart(string $guestSessionId): void
    {
        $guestCart = static::where('session_id', $guestSessionId)->first();
        if (!$guestCart || $guestCart->id === $this->id) {
            return;
        }

        foreach ($guestCart->items as $guestItem) {
            $existing = $this->items()
                ->where('product_id', $guestItem->product_id)
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->first();

            if ($existing) {
                $existing->update(['quantity' => $existing->quantity + $guestItem->quantity]);
            } else {
                $this->items()->create([
                    'product_id' => $guestItem->product_id,
                    'product_variant_id' => $guestItem->product_variant_id,
                    'quantity' => $guestItem->quantity,
                    'unit_price' => $guestItem->unit_price,
                ]);
            }
        }

        if ($guestCart->coupon_id && !$this->coupon_id) {
            $this->update(['coupon_id' => $guestCart->coupon_id]);
        }

        $guestCart->items()->delete();
        $guestCart->delete();
    }
}
