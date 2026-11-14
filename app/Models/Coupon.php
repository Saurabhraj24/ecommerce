<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount_amount',
        'start_date',
        'end_date',
        'usage_limit',
        'used_count',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function isValidForAmount(float $subtotal, ?string &$errorMessage = null): bool
    {
        if (!$this->is_active) {
            $errorMessage = 'This coupon is not active.';
            return false;
        }

        $now = Carbon::now();
        if ($this->start_date && $now->lt($this->start_date)) {
            $errorMessage = 'This coupon is not yet valid.';
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            $errorMessage = 'This coupon has expired.';
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            $errorMessage = 'This coupon usage limit has been reached.';
            return false;
        }

        if ($subtotal < $this->min_order_amount) {
            $errorMessage = "Minimum order value of ₹" . number_format($this->min_order_amount, 2) . " required.";
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->discount_type === 'percentage') {
            $discount = ($subtotal * $this->discount_value) / 100;
            if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
                $discount = (float) $this->max_discount_amount;
            }
            return min($discount, $subtotal);
        }

        return min((float) $this->discount_value, $subtotal);
    }
}
