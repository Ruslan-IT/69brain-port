<?php

namespace App\Filament\Pages;

use App\Filament\Forms\RichText;
use App\Filament\Pages\Concerns\EditsContent;
use App\Support\SiteContent;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageContacts extends Page
{
    use EditsContent;

    protected static ?string $navigationLabel = 'Контакты';

    protected static ?string $title = 'Контакты';

    protected static ?string $slug = 'kontakty';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Страница')
                ->schema([
                    TextInput::make('contacts_title')
                        ->label('Заголовок страницы')
                        ->helperText('Короткая строка над основным заголовком.')
                        ->maxLength(120),
                    TextInput::make('contacts_h1')
                        ->label('H1')
                        ->helperText('Единственный главный заголовок страницы. Это не SEO Title.')
                        ->required()
                        ->maxLength(180),
                    Textarea::make('contacts_lead')
                        ->label('Описание')
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Контактные данные')
                ->description('Телефон и почта также показываются в подвале. Пустые поля на сайте скрыты.')
                ->schema([
                    TextInput::make('phone')
                        ->label('Телефон')
                        ->tel()
                        ->maxLength(40)
                        ->helperText('Сейчас можно оставить временный номер и заменить его позже.'),
                    TextInput::make('email')
                        ->label('Электронная почта')
                        ->email()
                        ->maxLength(180),
                    Textarea::make('address')
                        ->label('Адрес')
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpanFull(),
                    Textarea::make('requisites')
                        ->label('Реквизиты')
                        ->rows(4)
                        ->maxLength(2000)
                        ->columnSpanFull(),
                    RichText::make('contact_extra')
                        ->label('Дополнительная контактная информация')
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('SEO')
                ->schema([
                    TextInput::make('contacts_seo_title')
                        ->label('SEO Title')
                        ->helperText('Попадает в тег title. Если пусто, используется общее значение.')
                        ->maxLength(180),
                    Textarea::make('contacts_seo_description')
                        ->label('SEO Description')
                        ->rows(3)
                        ->maxLength(320),
                ]),
        ]);
    }

    protected function contentFormData(): array
    {
        $options = SiteContent::ensureOptions();

        return [
            'contacts_title' => $options->contacts_title,
            'contacts_h1' => $options->contacts_h1,
            'contacts_lead' => $options->contacts_lead,
            'phone' => $options->phone,
            'email' => $options->email,
            'address' => $options->address,
            'requisites' => $options->requisites,
            'contact_extra' => $options->contact_extra,
            'contacts_seo_title' => $options->contacts_seo_title,
            'contacts_seo_description' => $options->contacts_seo_description,
        ];
    }

    protected function storeContent(array $data): void
    {
        SiteContent::ensureOptions()->update([
            'contacts_title' => $data['contacts_title'] ?? null,
            'contacts_h1' => $data['contacts_h1'] ?? null,
            'contacts_lead' => $data['contacts_lead'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'requisites' => $data['requisites'] ?? null,
            'contact_extra' => $data['contact_extra'] ?? null,
            'contacts_seo_title' => $data['contacts_seo_title'] ?? null,
            'contacts_seo_description' => $data['contacts_seo_description'] ?? null,
        ]);
    }
}
