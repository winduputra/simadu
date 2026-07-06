<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value'];

    private static string $cacheKey = 'system_settings_all';

    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::rememberForever(static::$cacheKey, function () {
            return static::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(static::$cacheKey);
    }

    public static function clearCache(): void
    {
        Cache::forget(static::$cacheKey);
    }
}
