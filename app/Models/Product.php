<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'price',
        'discount_price',
        'season',
        'fabric',
        'stock',
        'is_active',
        'is_featured',
        'is_new_arrival',
        'is_best_seller',
        'view_count',
        'meta_title',
        'meta_description',
        'created_by_id',
        'creator_name',
        'creator_email',
        'updated_by_id',
        'updater_name',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_best_seller' => 'boolean',
        'view_count' => 'integer',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('display_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    public function allVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function schoolUniform(): HasOne
    {
        return $this->hasOne(SchoolUniform::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true)->latest();
    }

    public function stockLogs(): HasMany
    {
        return $this->hasMany(StockLog::class)->latest();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNewArrivals($query)
    {
        return $query->where('is_new_arrival', true);
    }

    public function scopeBestSellers($query)
    {
        return $query->where('is_best_seller', true);
    }

    public function scopeSeason($query, string $season)
    {
        return $query->whereIn('season', [$season, 'all_season']);
    }

    // Computed attributes
    public function getEffectivePriceAttribute(): float
    {
        if ($this->discount_price && $this->discount_price > 0 && $this->discount_price < $this->price) {
            return (float) $this->discount_price;
        }

        return (float) $this->price;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->discount_price && $this->discount_price > 0 && $this->discount_price < $this->price) {
            return (int) round((($this->price - $this->discount_price) / $this->price) * 100);
        }

        return null;
    }

    public function getAverageRatingAttribute(): float
    {
        return (float) round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    public function getMainImageUrlAttribute(): ?string
    {
        $primary = $this->primaryImage;
        if ($primary) {
            return $primary->image_url;
        }

        $first = $this->images->first();
        return $first ? $first->image_url : null;
    }

    public function getHoverImageUrlAttribute(): ?string
    {
        $images = $this->images;
        if ($images->count() > 1) {
            return $images->get(1)->image_url;
        }

        return $this->main_image_url;
    }

    public function getTotalStockAttribute(): int
    {
        if ($this->variants()->exists()) {
            return (int) $this->variants()->sum('stock');
        }

        return (int) $this->stock;
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->total_stock > 0;
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}
