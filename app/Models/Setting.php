<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting || $setting->value === null) {
                return $default;
            }

            return match ($setting->type) {
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'number' => is_numeric($setting->value) ? $setting->value + 0 : $setting->value,
                'json' => json_decode($setting->value, true) ?? $default,
                default => $setting->value,
            };
        });
    }

    public static function set(string $key, mixed $value, string $type = 'text', string $group = 'store'): self
    {
        $serializedValue = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => is_array($value) ? json_encode($value) : (string) $value,
            default => (string) $value,
        };

        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $serializedValue, 'type' => $type, 'group' => $group]
        );

        Cache::forget("setting_{$key}");

        return $setting;
    }
}
