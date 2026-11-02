<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\EditsContent;
use App\Rules\SafeUrl;
use App\Support\SiteContent;
use App\Support\SiteSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSettings extends Page
{
    use EditsContent;

    protected static ?string $navigationLabel = 'Настройки сайта';

    protected static ?string $title = 'Настройки сайта';

    protected static ?string $slug = 'nastroyki';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Шапка')
                ->schema([
                    TextInput::make('logo_text')
                        ->label('Название или текст логотипа')
                        ->helperText('Тот же текст, что в блоке «Название компании» на главной.')
                        ->required()
                        ->maxLength(120),
                    TextInput::make('logo_url')
                        ->label('Ссылка логотипа')
                        ->helperText('Если пусто, логотип ведёт на главную.')
                        ->maxLength(255)
                        ->rule(new SafeUrl),
                    TextInput::make('header_button_label')
                        ->label('Текст кнопки')
                        ->maxLength(80),
                    TextInput::make('header_button_url')
                        ->label('Ссылка кнопки')
                        ->maxLength(255)
                        ->rule(new SafeUrl),
                ])
                ->columns(2),
            Section::make('Подвал')
                ->description('Пустые телефон, почта и адрес на сайте не показываются.')
                ->schema([
                    Textarea::make('footer_lead')
                        ->label('Описание')
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpanFull(),
                    TextInput::make('phone')
                        ->label('Телефон')
                        ->tel()
                        ->maxLength(40)
                        ->helperText('Общий телефон страницы контактов и подвала.'),
                    TextInput::make('email')
                        ->label('Электронная почта')
                        ->email()
                        ->maxLength(180),
                    TextInput::make('copyright')
                        ->label('Текст копирайта')
                        ->helperText('Год подставляется автоматически.')
                        ->maxLength(180)
                        ->columnSpanFull(),
                    Repeater::make('footer_links')
                        ->label('Ссылки')
                        ->schema([
                            TextInput::make('label')
                                ->label('Текст')
                                ->required()
                                ->maxLength(80),
                            TextInput::make('url')
                                ->label('Ссылка')
                                ->required()
                                ->maxLength(255)
                                ->rule(new SafeUrl),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->reorderable()
                        ->addActionLabel('Добавить ссылку')
                        ->deleteAction(fn (Action $action): Action => $action
                            ->requiresConfirmation()
                            ->modalHeading('Удалить ссылку?')
                            ->modalSubmitActionLabel('Удалить'))
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Общие SEO-настройки')
                ->description('Используются, если у страницы не заполнены собственные SEO Title и SEO Description.')
                ->schema([
                    TextInput::make('site_name')
                        ->label('Название сайта')
                        ->required()
                        ->maxLength(120)
                        ->columnSpanFull(),
                    TextInput::make('default_seo_title')
                        ->label('SEO Title по умолчанию')
                        ->maxLength(180),
                    Textarea::make('default_seo_description')
                        ->label('SEO Description по умолчанию')
                        ->rows(3)
                        ->maxLength(320),
                ]),
            Section::make('Визуализации')
                ->description('Те же переключатели, что и в разделе /enb.')
                ->schema([
                    Select::make('homepage_variant')
                        ->label('Вариант главной')
                        ->options([
                            'original' => 'Исходная главная',
                            'new' => 'Новая главная',
                        ])
                        ->required()
                        ->native(false),
                    Select::make('visualization_mode')
                        ->label('Режим визуализаций')
                        ->options([
                            'animated' => 'Анимация',
                            'static' => 'Статичные изображения',
                        ])
                        ->required()
                        ->native(false),
                ])
                ->columns(2),
        ]);
    }

    protected function contentFormData(): array
    {
        $options = SiteContent::ensureOptions();

        return [
            'logo_text' => $options->logo_text,
            'logo_url' => $options->logo_url,
            'header_button_label' => $options->header_button_label,
            'header_button_url' => $options->header_button_url,
            'footer_lead' => $options->footer_lead,
            'phone' => $options->phone,
            'email' => $options->email,
            'copyright' => $options->copyright,
            'footer_links' => array_values($options->footer_links ?? []),
            'site_name' => $options->site_name,
            'default_seo_title' => $options->default_seo_title,
            'default_seo_description' => $options->default_seo_description,
            'homepage_variant' => SiteSettings::homepage(),
            'visualization_mode' => SiteSettings::animated() ? 'animated' : 'static',
        ];
    }

    protected function storeContent(array $data): void
    {
        SiteContent::ensureOptions()->update([
            'logo_text' => $data['logo_text'] ?? null,
            'logo_url' => $data['logo_url'] ?? null,
            'header_button_label' => $data['header_button_label'] ?? null,
            'header_button_url' => $data['header_button_url'] ?? null,
            'footer_lead' => $data['footer_lead'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'copyright' => $data['copyright'] ?? null,
            'footer_links' => collect($data['footer_links'] ?? [])->map(fn (array $link): array => [
                'label' => $link['label'] ?? '',
                'url' => $link['url'] ?? '',
            ])->values()->all(),
            'site_name' => $data['site_name'] ?? null,
            'default_seo_title' => $data['default_seo_title'] ?? null,
            'default_seo_description' => $data['default_seo_description'] ?? null,
        ]);

        SiteSettings::put(SiteSettings::HOMEPAGE, ($data['homepage_variant'] ?? 'original') === 'new' ? 'new' : 'original');
        SiteSettings::put(SiteSettings::VISUALIZATION, ($data['visualization_mode'] ?? 'animated') === 'static' ? 'static' : 'animated');
    }
}
