<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * In-memory static cache for the current request lifecycle.
     */
    protected static array $runtimeCache = [];

    /**
     * Retrieve all settings as a cached key-value array.
     */
    public static function allCached(): array
    {
        return Cache::rememberForever('settings:all', function () {
            return static::pluck('value', 'key')->all();
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, static::$runtimeCache)) {
            return static::$runtimeCache[$key];
        }

        $all = static::allCached();
        if (array_key_exists($key, $all)) {
            return static::$runtimeCache[$key] = $all[$key];
        }

        return static::$runtimeCache[$key] = $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        static::$runtimeCache[$key] = $value;
        Cache::forget('settings:all');
        Cache::forget("setting:{$key}");
    }
}
