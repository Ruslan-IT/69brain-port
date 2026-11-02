<?php

namespace App\Filament\Resources;

use App\Filament\Forms\RichText;
use App\Filament\Resources\ServiceResource\Pages\CreateService;
use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Filament\Resources\ServiceResource\Pages\ListServices;
use App\Models\Service;
use App\Rules\SafeUrl;
use App\Support\SiteUrl;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $slug = 'napravleniya';

    protected static ?string $navigationLabel = 'Направления деятельности';

    protected static ?string $modelLabel = 'направление';

    protected static ?string $pluralModelLabel = 'Направления деятельности';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
            Section::make('Основное')
                ->columnSpanFull()
                ->schema([
                    TextInput::make('title')
                        ->label('Название')
                        ->required()
                        ->maxLength(180)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Get $get, Set $set, ?string $old, ?string $state, mixed $livewire): mixed => self::fillSlugFromTitle($get, $set, $old, $state, $livewire))
                        ->columnSpanFull(),
                    TextInput::make('slug')
                        ->label('Адрес')
                        ->prefix('/')
                        ->maxLength(180)
                        ->disabled(fn (?Service $record): bool => filled($record?->key))
                        ->dehydrated()
                        ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? $state : null)
                        ->nullable()
                        ->unique(ignoreRecord: true)
                        ->rule(fn (): Closure => self::slugRule())
                        ->helperText(fn (?Service $record): string => filled($record?->key)
                            ? 'Адрес этой страницы закреплён, чтобы существующие ссылки продолжали работать.'
                            : 'Появляется из названия. Можно изменить: строчные латинские буквы, цифры и дефис.'),
                    Toggle::make('is_published')
                        ->label('Опубликовано')
                        ->default(true)
                        ->helperText('Скрытое направление не показывается на сайте и открывается с ошибкой 404.'),
                    Textarea::make('excerpt')
                        ->label('Краткое описание')
                        ->helperText('Текст карточки на главной странице.')
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Страница')
                ->columnSpanFull()
                ->schema([
                    RichText::make('lead')
                        ->label('Основной текст')
                        ->helperText('Абзац под заголовком страницы.')
                        ->columnSpanFull(),
                ]),
            Section::make('Содержание')
                ->columnSpanFull()
                ->schema([
                    Repeater::make('blocks')
                        ->label('Блоки')
                        ->columnSpanFull()
                        ->schema([
                            Hidden::make('icon'),
                            Hidden::make('anchor'),
                            TextInput::make('title')
                                ->label('Заголовок')
                                ->required()
                                ->maxLength(180)
                                ->columnSpanFull(),
                            TextInput::make('nav')
                                ->label('Текст в содержании')
                                ->helperText('Если пусто, в содержании используется заголовок.')
                                ->maxLength(180)
                                ->columnSpanFull(),
                            RichText::make('body')
                                ->label('Текст')
                                ->columnSpanFull(),
                            RichText::make('extra')
                                ->label('Дополнительное описание')
                                ->columnSpanFull(),
                            TextInput::make('link_label')
                                ->label('Текст ссылки')
                                ->maxLength(180)
                                ->helperText('Ссылка показывается, только если заполнены текст и адрес.'),
                            TextInput::make('link_url')
                                ->label('Ссылка')
                                ->maxLength(255)
                                ->rule(new SafeUrl),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                        ->addActionLabel('Добавить блок')
                        ->deleteAction(fn (Action $action): Action => $action
                            ->requiresConfirmation()
                            ->modalHeading('Удалить блок?')
                            ->modalSubmitActionLabel('Удалить')),
                ]),
            Section::make('SEO-настройки')
                ->columnSpanFull()
                ->collapsed()
                ->schema([
                    TextInput::make('seo_title')
                        ->label('SEO Title')
                        ->helperText('Попадает в тег title. Если пусто, используется название направления.')
                        ->maxLength(180)
                        ->columnSpanFull(),
                    Textarea::make('seo_description')
                        ->label('SEO Description')
                        ->helperText('Если пусто, используется краткое описание.')
                        ->rows(3)
                        ->maxLength(320)
                        ->columnSpanFull(),
                    TextInput::make('h1')
                        ->label('H1')
                        ->helperText('Единственный главный заголовок страницы. Это не SEO Title.')
                        ->required()
                        ->maxLength(180)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('slug')
                    ->label('Адрес')
                    ->formatStateUsing(fn (Service $record): string => $record->publicPath()),
                ToggleColumn::make('is_published')
                    ->label('На сайте'),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Удалить направление?')
                    ->modalDescription('Страница исчезнет с сайта. Это действие нельзя отменить.')
                    ->modalSubmitActionLabel('Удалить'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }

    private static function fillSlugFromTitle(Get $get, Set $set, ?string $old, ?string $state, mixed $livewire): void
    {
        if (! $livewire instanceof CreateRecord) {
            return;
        }

        $current = trim((string) ($get('slug') ?? ''));
        $generated = filled($old) ? Service::uniqueSlug((string) $old) : '';

        if ($current !== '' && $current !== $generated) {
            return;
        }

        $set('slug', filled($state) ? Service::uniqueSlug((string) $state) : null);
    }

    private static function slugRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if ($value === null || $value === '') {
                return;
            }

            if (preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', (string) $value) !== 1) {
                $fail('Адрес может содержать только строчные латинские буквы, цифры и дефис.');

                return;
            }

            if (in_array($value, SiteUrl::reserved(), true)) {
                $fail('Этот адрес занят системой.');
            }
        };
    }
}
