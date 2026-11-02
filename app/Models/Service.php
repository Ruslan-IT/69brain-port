<?php

namespace App\Models;

use App\Support\SiteContent;
use App\Support\SiteUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'blocks' => 'array',
            'is_published' => 'boolean',
            'sort' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Service $service): void {
            if (blank($service->slug)) {
                $service->slug = static::uniqueSlug((string) $service->title, $service->exists ? $service->id : null);
            }

            if (blank($service->h1)) {
                $service->h1 = $service->title;
            }

            if ($service->blocks === null) {
                $service->blocks = [];
            }

            if ($service->sort === null) {
                $service->sort = ((int) static::query()->max('sort')) + 1;
            }

            if ($service->is_published === null) {
                $service->is_published = true;
            }
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = trim(Str::slug(Str::limit($title, 160, ''), '-', 'ru'), '-');
        $base = trim(Str::limit($base !== '' ? $base : 'napravlenie', 80, ''), '-');

        if ($base === '') {
            $base = 'napravlenie';
        }

        $slug = $base;
        $suffix = 2;

        while (static::slugTaken($slug, $ignoreId)) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    public function displayH1(): string
    {
        return filled($this->h1) ? (string) $this->h1 : (string) $this->title;
    }

    public function documentTitle(): string
    {
        if (filled($this->seo_title)) {
            return (string) $this->seo_title;
        }

        $name = filled($this->title) ? (string) $this->title : 'Страница';

        return $name.' — '.SiteContent::options()->displaySiteName();
    }

    public function documentDescription(): string
    {
        if (filled($this->seo_description)) {
            return (string) $this->seo_description;
        }

        $plain = trim(preg_replace('/\s+/u', ' ', strip_tags((string) ($this->excerpt ?: $this->lead))) ?? '');

        if ($plain !== '') {
            return $plain;
        }

        return (string) (SiteContent::options()->default_seo_description ?: '');
    }

    public function hasScene(): bool
    {
        return in_array($this->key, ['strength', 'statistics', 'technology'], true);
    }

    public function publicUrl(): string
    {
        if (filled($this->key) && \Illuminate\Support\Facades\Route::has((string) $this->key)) {
            return route((string) $this->key);
        }

        return route('service.show', ['slug' => $this->slug]);
    }

    public function publicPath(): string
    {
        return (string) (parse_url($this->publicUrl(), PHP_URL_PATH) ?: '/'.$this->slug);
    }

    public function isCurrent(): bool
    {
        if (filled($this->key) && request()->routeIs((string) $this->key)) {
            return true;
        }

        return request()->routeIs('service.show') && request()->route('slug') === $this->slug;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    public static function syncCards(array $rows): void
    {
        foreach (array_values($rows) as $index => $row) {
            if (! is_array($row) || blank($row['title'] ?? null)) {
                continue;
            }

            $payload = [
                'title' => $row['title'],
                'excerpt' => $row['excerpt'] ?? null,
                'card_link_label' => filled($row['card_link_label'] ?? null) ? $row['card_link_label'] : null,
                'is_published' => (bool) ($row['is_published'] ?? false),
                'sort' => $index + 1,
            ];

            $id = $row['id'] ?? null;
            $service = filled($id) ? static::query()->find($id) : null;

            if ($service instanceof self) {
                $service->update($payload);

                continue;
            }

            static::query()->create($payload + [
                'h1' => $row['title'],
                'lead' => $row['excerpt'] ?? null,
                'blocks' => [],
            ]);
        }
    }

    /**
     * @return list<array{title: string, body: string, extra: string, link_label: string, link_url: ?string, icon: ?string, anchor: string, nav: string, index: string, extra_sheet: bool}>
     */
    public function contentBlocks(): array
    {
        $used = [];
        $items = [];

        foreach (array_values($this->blocks ?? []) as $position => $block) {
            if (! is_array($block)) {
                continue;
            }

            $title = trim((string) ($block['title'] ?? ''));
            $body = (string) ($block['body'] ?? '');
            $extra = (string) ($block['extra'] ?? '');

            if ($title === '' && trim(strip_tags($body.$extra)) === '') {
                continue;
            }

            $base = trim((string) ($block['anchor'] ?? ''));

            if ($base === '' || preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $base) !== 1) {
                $base = Str::slug($title) ?: 'blok';
            }

            $anchor = $base;
            $suffix = 2;

            while (isset($used[$anchor])) {
                $anchor = $base.'-'.$suffix;
                $suffix++;
            }

            $used[$anchor] = true;
            $nav = trim((string) ($block['nav'] ?? ''));
            $label = trim((string) ($block['link_label'] ?? ''));
            $url = SiteUrl::href($block['link_url'] ?? null);
            $icon = $block['icon'] ?? null;

            $items[] = [
                'title' => $title,
                'body' => $body,
                'extra' => $extra,
                'link_label' => $label,
                'link_url' => ($label !== '' && $url !== null) ? $url : null,
                'icon' => in_array($icon, ['plate', 'drone', 'frame', 'stamp', 'laser'], true) ? $icon : null,
                'anchor' => $anchor,
                'nav' => $nav !== '' ? $nav : $title,
                'index' => sprintf('%02d', count($items) + 1),
                'extra_sheet' => $this->key === 'technology' && $position >= 2,
            ];
        }

        return $items;
    }

    private static function slugTaken(string $slug, ?int $ignoreId): bool
    {
        if (in_array($slug, SiteUrl::reserved(), true)) {
            return true;
        }

        return static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }
}
