<?php

namespace App\Filament\Pages;

use App\Filament\Forms\RichText;
use App\Filament\Pages\Concerns\EditsContent;
use App\Rules\PhoneNumber;
use App\Support\SiteContent;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                        ->type('tel')
                        ->rule(new PhoneNumber)
                        ->maxLength(40)
                        ->helperText('Можно сохранить как +7 (988) 096-20-14, +79880962014, 8 (988) 096-20-14 или +7 988 096-20-14. Показывается на странице контактов и в подвале.'),
                    TextInput::make('email')
                        ->label('Электронная почта')
                        ->email()
                        ->maxLength(180)
                        ->helperText('На этот адрес также приходит уведомление о новой заявке.'),
                    TextInput::make('inn')
                        ->label('ИНН исполнителя')
                        ->maxLength(20)
                        ->helperText('Это ИНН исполнителя, не ИНН банка.')
                        ->columnSpanFull(),
                    Textarea::make('address')
                        ->label('Адрес')
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpanFull(),
                    Textarea::make('requisites')
                        ->label('Реквизиты')
                        ->rows(4)
                        ->maxLength(2000)
                        ->helperText('Этот текст виден на странице контактов. Номер счёта сюда не копируйте.')
                        ->columnSpanFull(),
                    Textarea::make('tax_note')
                        ->label('Налоговая информация')
                        ->rows(3)
                        ->maxLength(1000)
                        ->helperText('Пока подтверждение из налоговой не пришло, оставьте поле пустым. На сайте появится только записанный здесь текст, без автоматической пометки о статусе.')
                        ->columnSpanFull(),
                    RichText::make('contact_extra')
                        ->label('Дополнительная контактная информация')
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Банковские реквизиты')
                ->description('Пока переключатель выключен, эти данные не показываются посетителям. Для приёма платежей они не используются.')
                ->collapsed()
                ->schema([
                    Toggle::make('bank_details_visible')
                        ->label('Показывать на странице контактов')
                        ->default(false)
                        ->columnSpanFull(),
                    TextInput::make('bank_currency')
                        ->label('Валюта перевода')
                        ->maxLength(120),
                    TextInput::make('bank_recipient')
                        ->label('Получатель')
                        ->maxLength(180),
                    TextInput::make('bank_account')
                        ->label('Номер счёта')
                        ->maxLength(34),
                    TextInput::make('bank_name')
                        ->label('Банк получателя')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    TextInput::make('bank_bik')
                        ->label('БИК')
                        ->maxLength(20),
                    TextInput::make('bank_correspondent_account')
                        ->label('Корреспондентский счёт')
                        ->maxLength(34),
                    TextInput::make('bank_inn')
                        ->label('ИНН банка')
                        ->maxLength(20)
                        ->helperText('Не путать с ИНН исполнителя.'),
                    TextInput::make('bank_kpp')
                        ->label('КПП')
                        ->maxLength(20),
                    TextInput::make('bank_okpo')
                        ->label('ОКПО')
                        ->maxLength(20),
                    TextInput::make('bank_ogrn')
                        ->label('ОГРН')
                        ->maxLength(20),
                    TextInput::make('bank_swift')
                        ->label('SWIFT')
                        ->maxLength(20),
                    Textarea::make('bank_address')
                        ->label('Почтовый адрес банка')
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull(),
                    Textarea::make('bank_office_address')
                        ->label('Почтовый адрес дополнительного офиса')
                        ->rows(2)
                        ->maxLength(500)
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
            'inn' => $options->inn,
            'address' => $options->address,
            'requisites' => $options->requisites,
            'tax_note' => $options->tax_note,
            'contact_extra' => $options->contact_extra,
            'bank_details_visible' => (bool) $options->bank_details_visible,
            'bank_currency' => $options->bank_currency,
            'bank_recipient' => $options->bank_recipient,
            'bank_account' => $options->bank_account,
            'bank_name' => $options->bank_name,
            'bank_bik' => $options->bank_bik,
            'bank_correspondent_account' => $options->bank_correspondent_account,
            'bank_inn' => $options->bank_inn,
            'bank_kpp' => $options->bank_kpp,
            'bank_okpo' => $options->bank_okpo,
            'bank_ogrn' => $options->bank_ogrn,
            'bank_swift' => $options->bank_swift,
            'bank_address' => $options->bank_address,
            'bank_office_address' => $options->bank_office_address,
            'contacts_seo_title' => $options->contacts_seo_title,
            'contacts_seo_description' => $options->contacts_seo_description,
        ];
    }

    protected function storeContent(array $data): void
    {
        SiteContent::ensureOptions()->update([
            'contacts_title' => $this->nullableString($data['contacts_title'] ?? null),
            'contacts_h1' => $this->nullableString($data['contacts_h1'] ?? null),
            'contacts_lead' => $this->nullableString($data['contacts_lead'] ?? null),
            'phone' => $this->nullableString($data['phone'] ?? null),
            'email' => $this->nullableString($data['email'] ?? null),
            'inn' => $this->nullableString($data['inn'] ?? null),
            'address' => $this->nullableString($data['address'] ?? null),
            'requisites' => $this->nullableString($data['requisites'] ?? null),
            'tax_note' => $this->nullableString($data['tax_note'] ?? null),
            'contact_extra' => $data['contact_extra'] ?? null,
            'bank_details_visible' => (bool) ($data['bank_details_visible'] ?? false),
            'bank_currency' => $this->nullableString($data['bank_currency'] ?? null),
            'bank_recipient' => $this->nullableString($data['bank_recipient'] ?? null),
            'bank_account' => $this->nullableString($data['bank_account'] ?? null),
            'bank_name' => $this->nullableString($data['bank_name'] ?? null),
            'bank_bik' => $this->nullableString($data['bank_bik'] ?? null),
            'bank_correspondent_account' => $this->nullableString($data['bank_correspondent_account'] ?? null),
            'bank_inn' => $this->nullableString($data['bank_inn'] ?? null),
            'bank_kpp' => $this->nullableString($data['bank_kpp'] ?? null),
            'bank_okpo' => $this->nullableString($data['bank_okpo'] ?? null),
            'bank_ogrn' => $this->nullableString($data['bank_ogrn'] ?? null),
            'bank_swift' => $this->nullableString($data['bank_swift'] ?? null),
            'bank_address' => $this->nullableString($data['bank_address'] ?? null),
            'bank_office_address' => $this->nullableString($data['bank_office_address'] ?? null),
            'contacts_seo_title' => $this->nullableString($data['contacts_seo_title'] ?? null),
            'contacts_seo_description' => $this->nullableString($data['contacts_seo_description'] ?? null),
        ]);
    }
}
