<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'color_code',
        'sku',
        'additional_price',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'additional_price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        $base = $this->product->effective_price;
        return (float) ($base + $this->additional_price);
    }

    public function getLabelAttribute(): string
    {
        $parts = [];
        if ($this->size) {
            $parts[] = "Size: {$this->size}";
        }
        if ($this->color) {
            $parts[] = "Color: {$this->color}";
        }
        return implode(', ', $parts) ?: 'Default';
    }
}
