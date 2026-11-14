<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'shipping_charge',
        'grand_total',
        'coupon_code',
        'payment_method',
        'payment_status',
        'order_status',
        'notes',
        'tracking_number',
        'courier_name',
        'cancelled_reason',
        'return_reason',
        'return_status',
        'delivered_at',
        'billed_by_id',
        'cashier_name',
        'cashier_email',
        'cashier_role',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_charge' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'delivered_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function billedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'billed_by_id');
    }

    public function isPos(): bool
    {
        return str_starts_with($this->order_number, 'BF-POS-') || !empty($this->billed_by_id) || !empty($this->cashier_name);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function payment(): HasOne
    {
        return $this->latestPayment();
    }

    public function getStatusAttribute(): string
    {
        return $this->order_status;
    }

    public function setStatusAttribute($value): void
    {
        $this->attributes['order_status'] = $value;
    }

    public function getShippingAmountAttribute(): float
    {
        return (float) $this->shipping_charge;
    }

    public function getShippingNameAttribute(): string
    {
        return $this->shipping_address['name'] ?? $this->customer_name ?? '';
    }

    public function getShippingPhoneAttribute(): string
    {
        return $this->shipping_address['phone'] ?? $this->customer_phone ?? '';
    }

    public function getShippingEmailAttribute(): string
    {
        return $this->customer_email ?? '';
    }

    public function getShippingAddressLine1Attribute(): string
    {
        return $this->shipping_address['address_line1'] ?? '';
    }

    public function getShippingAddressLine2Attribute(): ?string
    {
        return $this->shipping_address['address_line2'] ?? null;
    }

    public function getShippingCityAttribute(): string
    {
        return $this->shipping_address['city'] ?? 'Amritsar';
    }

    public function getShippingStateAttribute(): string
    {
        return $this->shipping_address['state'] ?? 'Punjab';
    }

    public function getShippingPincodeAttribute(): string
    {
        return $this->shipping_address['pincode'] ?? '143001';
    }

    public function canCancel(): bool
    {
        return in_array($this->order_status, ['pending', 'confirmed']);
    }

    public function canReturn(): bool
    {
        if ($this->order_status !== 'delivered') {
            return false;
        }

        if ($this->return_status !== 'none') {
            return false;
        }

        // Return allowed within 7 days of delivery
        if ($this->delivered_at && $this->delivered_at->addDays(7)->isPast()) {
            return false;
        }

        return true;
    }

    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        $count = static::whereYear('created_at', $year)->count() + 1;
        $sequence = str_pad($count, 4, '0', STR_PAD_LEFT);

        return "BF-{$year}-{$sequence}{$random}";
    }
}
