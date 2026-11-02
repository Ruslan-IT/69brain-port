<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SiteSettings
{
    public const HOMEPAGE = 'homepage_variant';

    public const VISUALIZATION = 'visualization_mode';

    public static function get(string $key, string $default): string
    {
        $all = self::all();

        return $all[$key] ?? $default;
    }

    public static function animated(): bool
    {
        return self::get(self::VISUALIZATION, 'animated') === 'animated';
    }

    public static function homepage(): string
    {
        $value = self::get(self::HOMEPAGE, 'original');

        return $value === 'new' ? 'new' : 'original';
    }

    public static function put(string $key, string $value): void
    {
        Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('site.settings');
    }

    /**
     * @return array<string, string>
     */
    private static function all(): array
    {
        try {
            if (! Schema::hasTable('settings')) {
                return [];
            }

            return Cache::remember('site.settings', 60, function () {
                return Setting::query()->pluck('value', 'key')->all();
            });
        } catch (Throwable) {
            return [];
        }
    }
}
