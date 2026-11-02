<?php

namespace App\Models;

use App\Support\SiteContent;
use App\Support\SiteUrl;
use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'works' => 'array',
        ];
    }

    public function displayH1(): string
    {
        $h1 = trim((string) $this->h1);

        return $h1 !== '' ? $h1 : 'КБ «Прочность»';
    }

    public function documentTitle(): string
    {
        if (filled($this->seo_title)) {
            return (string) $this->seo_title;
        }

        $fallback = SiteContent::options()->default_seo_title;

        return filled($fallback) ? (string) $fallback : SiteContent::options()->displaySiteName();
    }

    public function documentDescription(): string
    {
        if (filled($this->seo_description)) {
            return (string) $this->seo_description;
        }

        return (string) (SiteContent::options()->default_seo_description ?: '');
    }

    /**
     * @return list<array{title: string, body: string, link_label: string, link_url: ?string, index: string}>
     */
    public function workItems(): array
    {
        $items = [];

        foreach (array_values($this->works ?? []) as $item) {
            if (! is_array($item)) {
                continue;
            }

            $title = trim((string) ($item['title'] ?? ''));
            $body = (string) ($item['body'] ?? '');

            if ($title === '' && trim(strip_tags($body)) === '') {
                continue;
            }

            $label = trim((string) ($item['link_label'] ?? ''));
            $url = SiteUrl::href($item['link_url'] ?? null);

            $items[] = [
                'title' => $title,
                'body' => $body,
                'link_label' => $label,
                'link_url' => ($label !== '' && $url !== null) ? $url : null,
                'index' => sprintf('%02d', count($items) + 1),
            ];
        }

        return $items;
    }
}
