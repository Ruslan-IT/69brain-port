<?php

namespace App\Models;

use App\Support\SiteUrl;
use Illuminate\Database\Eloquent\Model;

class SiteOption extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'footer_links' => 'array',
        ];
    }

    public function displaySiteName(): string
    {
        return filled($this->site_name) ? (string) $this->site_name : (string) config('site.name');
    }

    public function logoHref(): string
    {
        return SiteUrl::href($this->logo_url) ?: route('home');
    }

    /**
     * @return array{label: string, url: string}|null
     */
    public function headerButton(): ?array
    {
        $label = trim((string) $this->header_button_label);
        $url = SiteUrl::href($this->header_button_url);

        if ($label === '' || $url === null) {
            return null;
        }

        return ['label' => $label, 'url' => $url];
    }

    /**
     * @return list<array{label: string, url: string}>
     */
    public function footerLinkItems(): array
    {
        $items = [];

        foreach (array_values($this->footer_links ?? []) as $link) {
            if (! is_array($link)) {
                continue;
            }

            $label = trim((string) ($link['label'] ?? ''));
            $url = SiteUrl::href($link['url'] ?? null);

            if ($label === '' || $url === null) {
                continue;
            }

            $items[] = ['label' => $label, 'url' => $url];
        }

        return $items;
    }

    public function copyrightLine(): string
    {
        $name = filled($this->copyright) ? (string) $this->copyright : $this->displaySiteName();

        return '© '.now()->year.' '.$name;
    }

    public function contactsH1(): string
    {
        return filled($this->contacts_h1) ? (string) $this->contacts_h1 : 'Контакты';
    }

    public function contactsDocumentTitle(): string
    {
        if (filled($this->contacts_seo_title)) {
            return (string) $this->contacts_seo_title;
        }

        if (filled($this->default_seo_title)) {
            return (string) $this->default_seo_title;
        }

        return 'Контакты — '.$this->displaySiteName();
    }

    public function contactsDocumentDescription(): string
    {
        if (filled($this->contacts_seo_description)) {
            return (string) $this->contacts_seo_description;
        }

        return (string) ($this->default_seo_description ?: '');
    }

    public function phoneLink(): ?string
    {
        $digits = preg_replace('/[^\d+]/', '', (string) $this->phone) ?? '';

        return $digits !== '' ? 'tel:'.$digits : null;
    }

    public function emailLink(): ?string
    {
        $email = trim((string) $this->email);

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? 'mailto:'.$email : null;
    }

    public function hasContactDetails(): bool
    {
        return filled($this->phone)
            || filled($this->email)
            || filled($this->address)
            || filled($this->requisites)
            || trim(strip_tags((string) $this->contact_extra)) !== '';
    }
}
