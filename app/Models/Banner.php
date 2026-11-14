<?php

namespace App\Models;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'link_url',
        'button_text',
        'image_url',
        'public_id',
        'position',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Banner $banner) {
            if ($banner->public_id) {
                try {
                    Cloudinary::destroy($banner->public_id);
                } catch (\Throwable $e) {
                    Log::warning("Failed to delete Cloudinary banner asset {$banner->public_id}: " . $e->getMessage());
                }
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }

    public function scopePosition($query, string $position)
    {
        return $query->where('position', $position);
    }
}
