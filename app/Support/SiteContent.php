<?php

namespace App\Support;

use App\Models\HomePage;
use App\Models\Service;
use App\Models\SiteOption;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class SiteContent
{
    public static function home(): HomePage
    {
        if (self::ready()) {
            $home = HomePage::query()->first();

            if ($home instanceof HomePage) {
                return $home;
            }
        }

        return new HomePage(ContentCatalog::home());
    }

    public static function options(): SiteOption
    {
        if (self::ready()) {
            $options = SiteOption::query()->first();

            if ($options instanceof SiteOption) {
                return $options;
            }
        }

        return new SiteOption(ContentCatalog::options());
    }

    /**
     * @return Collection<int, Service>
     */
    public static function services(): Collection
    {
        if (! self::ready()) {
            return collect(ContentCatalog::services());
        }

        return Service::query()
            ->where('is_published', true)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();
    }

    public static function findByKey(string $key): ?Service
    {
        if (! self::ready()) {
            return ContentCatalog::service($key);
        }

        return Service::query()->where('key', $key)->first();
    }

    public static function findPublishedBySlug(string $slug): ?Service
    {
        if (! self::ready()) {
            return null;
        }

        return Service::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();
    }

    public static function ensureHome(): HomePage
    {
        self::assertReady();

        return HomePage::query()->first() ?? HomePage::query()->create(ContentCatalog::home());
    }

    public static function ensureOptions(): SiteOption
    {
        self::assertReady();

        return SiteOption::query()->first() ?? SiteOption::query()->create(ContentCatalog::options());
    }

    private static function assertReady(): void
    {
        if (! self::ready()) {
            throw new RuntimeException('Таблицы контента ещё не созданы. Выполните миграции.');
        }
    }

    private static function ready(): bool
    {
        try {
            return Schema::hasTable('home_pages')
                && Schema::hasTable('services')
                && Schema::hasTable('site_options');
        } catch (Throwable) {
            return false;
        }
    }
}
