<?php

namespace App\Support;

class SiteUrl
{
    /**
     * @return list<string>
     */
    public static function reserved(): array
    {
        return [
            'admin',
            'enb',
            'kontakty',
            'livewire',
            'storage',
            'up',
            'css',
            'js',
            'images',
            'vendor',
            'build',
            'fonts',
            'sanctum',
        ];
    }

    public static function slugPattern(): string
    {
        $reserved = implode('|', array_map(
            static fn (string $slug): string => preg_quote($slug, '/'),
            self::reserved(),
        ));

        return '^(?!(?:'.$reserved.')$)[a-z0-9]+(?:-[a-z0-9]+)*$';
    }

    public static function href(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '' || preg_match('/[\s<>"\']/', $url) === 1) {
            return null;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//') && ! str_starts_with($url, '/\\')) {
            return $url;
        }

        if (str_starts_with($url, '#') && ! str_contains($url, '//')) {
            return $url;
        }

        if (preg_match('#^(https?://|mailto:|tel:)#i', $url) === 1) {
            return $url;
        }

        return null;
    }
}
