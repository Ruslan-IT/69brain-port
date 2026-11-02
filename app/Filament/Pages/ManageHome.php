<?php

namespace App\Filament\Pages;

use App\Filament\Forms\RichText;
use App\Filament\Pages\Concerns\EditsContent;
use App\Models\Service;
use App\Rules\SafeUrl;
use App\Support\SiteContent;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class ManageHome extends Page
{
    use EditsContent;

    protected static ?string $navigationLabel = 'Главная страница';

    protected static ?string $title = 'Главная страница';

    protected static ?string $slug = 'glavnaya';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Первый экран')
                ->extraAttributes(['class' => 'home-block home-block--hero'])
                ->description('Тексты над инженерной визуализацией. Композиция экрана не меняется.')
                ->schema([
                    TextInput::make('logo_text')
                        ->label('Название компании')
                        ->helperText('Текст логотипа в шапке и подвале.')
                        ->required()
                        ->maxLength(120),
                    TextInput::make('eyebrow')
                        ->label('Надзаголовок')
                        ->maxLength(120),
                    Textarea::make('h1')
                        ->label('H1 — главный заголовок')
                        ->helperText('Новая строка переносится на сайте. Это не SEO Title.')
                        ->required()
                        ->rows(2)
                        ->maxLength(180)
                        ->columnSpanFull(),
                    TextInput::make('subtitle')
                        ->label('Подзаголовок')
                        ->helperText('Если заполнить, строка появится между заголовком и описанием.')
                        ->maxLength(180)
                        ->columnSpanFull(),
                    Textarea::make('description')
                        ->label('Описание')
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpanFull(),
                    TextInput::make('button_label')
                        ->label('Текст кнопки')
                        ->maxLength(80),
                    TextInput::make('button_url')
                        ->label('Ссылка кнопки')
                        ->maxLength(255)
                        ->rule(new SafeUrl),
                ])
                ->columns(2),
            Section::make('Что мы делаем?')
                ->extraAttributes(['class' => 'home-block home-block--works'])
                ->schema([
                    TextInput::make('works_title')
                        ->label('Заголовок блока')
                        ->required()
                        ->maxLength(180),
                    Repeater::make('works')
                        ->label('Элементы')
                        ->schema([
                            TextInput::make('title')
                                ->label('Заголовок')
                                ->required()
                                ->maxLength(180)
                                ->columnSpanFull(),
                            RichText::make('body')
                                ->label('Описание')
                                ->required()
                                ->columnSpanFull(),
                            TextInput::make('link_label')
                                ->label('Текст ссылки')
                                ->maxLength(180)
                                ->helperText('Если текст или ссылка пустые, ссылка на сайте не показывается.'),
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
                        ->addActionLabel('Добавить элемент')
                        ->deleteAction(fn (Action $action): Action => $action
                            ->requiresConfirmation()
                            ->modalHeading('Удалить элемент?')
                            ->modalSubmitActionLabel('Удалить'))
                        ->columnSpanFull(),
                ]),
            Section::make('Кто мы?')
                ->extraAttributes(['class' => 'home-block home-block--who'])
                ->schema([
                    TextInput::make('who_title')
                        ->label('Заголовок блока')
                        ->required()
                        ->maxLength(180),
                    RichText::make('who_text')
                        ->label('Основной текст')
                        ->required(),
                    RichText::make('who_extra')
                        ->label('Дополнительный текст')
                        ->helperText('Необязательный текст под основной фразой.'),
                ]),
            Section::make('Направления деятельности')
                ->extraAttributes(['class' => 'home-block home-block--directions'])
                ->description('Карточки на главной. Текст страницы направления редактируется в разделе «Направления деятельности».')
                ->schema([
                    TextInput::make('directions_title')
                        ->label('Заголовок блока')
                        ->required()
                        ->maxLength(180),
                    Repeater::make('directions')
                        ->label('Направления')
                        ->schema([
                            Hidden::make('id'),
                            TextInput::make('title')
                                ->label('Заголовок')
                                ->required()
                                ->maxLength(180)
                                ->columnSpanFull(),
                            Textarea::make('excerpt')
                                ->label('Краткое описание')
                                ->rows(3)
                                ->maxLength(500)
                                ->columnSpanFull(),
                            TextInput::make('card_link_label')
                                ->label('Текст ссылки')
                                ->maxLength(80)
                                ->default('Подробнее'),
                            Toggle::make('is_published')
                                ->label('Показывать на сайте')
                                ->default(true),
                            Placeholder::make('public_path')
                                ->label('Страница')
                                ->content(function (Get $get): string {
                                    $id = $get('id');

                                    if (blank($id)) {
                                        return 'Ссылка появится после сохранения и будет вести на страницу направления.';
                                    }

                                    $service = Service::query()->find($id);

                                    return $service instanceof Service
                                        ? $service->publicPath()
                                        : 'Страница направления';
                                })
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->reorderable()
                        ->deletable(false)
                        ->addActionLabel('Добавить направление')
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                        ->helperText('Новая карточка создаёт направление. Удаление страницы — в разделе «Направления деятельности».')
                        ->columnSpanFull(),
                ]),
            Section::make('Есть инженерная задача?')
                ->extraAttributes(['class' => 'home-block home-block--cta'])
                ->schema([
                    TextInput::make('cta_title')
                        ->label('Заголовок')
                        ->maxLength(180)
                        ->columnSpanFull(),
                    Textarea::make('cta_text')
                        ->label('Описание')
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpanFull(),
                    TextInput::make('cta_button_label')
                        ->label('Текст кнопки')
                        ->maxLength(80),
                    TextInput::make('cta_button_url')
                        ->label('Ссылка кнопки')
                        ->maxLength(255)
                        ->rule(new SafeUrl),
                ])
                ->columns(2),
            Section::make('SEO')
                ->extraAttributes(['class' => 'home-block home-block--seo'])
                ->description('Если поле пустое, используется общее значение из настроек сайта.')
                ->schema([
                    TextInput::make('seo_title')
                        ->label('SEO Title')
                        ->helperText('Попадает в тег title. Это не заголовок H1.')
                        ->maxLength(180),
                    Textarea::make('seo_description')
                        ->label('SEO Description')
                        ->rows(3)
                        ->maxLength(320),
                ]),
        ]);
    }

    protected function contentFormData(): array
    {
        $home = SiteContent::ensureHome();
        $options = SiteContent::ensureOptions();

        return [
            'logo_text' => $options->logo_text,
            'eyebrow' => $home->eyebrow,
            'h1' => $home->h1,
            'subtitle' => $home->subtitle,
            'description' => $home->description,
            'button_label' => $home->button_label,
            'button_url' => $home->button_url,
            'works_title' => $home->works_title,
            'works' => array_values($home->works ?? []),
            'who_title' => $home->who_title,
            'who_text' => $home->who_text,
            'who_extra' => $home->who_extra,
            'directions_title' => $home->directions_title,
            'directions' => Service::query()->orderBy('sort')->orderBy('id')->get()->map(fn (Service $service): array => [
                'id' => $service->id,
                'title' => $service->title,
                'excerpt' => $service->excerpt,
                'card_link_label' => $service->card_link_label,
                'is_published' => $service->is_published,
            ])->all(),
            'cta_title' => $home->cta_title,
            'cta_text' => $home->cta_text,
            'cta_button_label' => $home->cta_button_label,
            'cta_button_url' => $home->cta_button_url,
            'seo_title' => $home->seo_title,
            'seo_description' => $home->seo_description,
        ];
    }

    protected function storeContent(array $data): void
    {
        $directions = array_values($data['directions'] ?? []);
        $logo = $data['logo_text'] ?? null;

        DB::transaction(function () use ($data, $directions, $logo): void {
            SiteContent::ensureHome()->update([
                'eyebrow' => $data['eyebrow'] ?? null,
                'h1' => $data['h1'],
                'subtitle' => $data['subtitle'] ?? null,
                'description' => $data['description'] ?? null,
                'button_label' => $data['button_label'] ?? null,
                'button_url' => $data['button_url'] ?? null,
                'works_title' => $data['works_title'],
                'works' => collect($data['works'] ?? [])->map(fn (array $item): array => [
                    'title' => $item['title'] ?? '',
                    'body' => $item['body'] ?? '',
                    'link_label' => $item['link_label'] ?? '',
                    'link_url' => $item['link_url'] ?? '',
                ])->values()->all(),
                'who_title' => $data['who_title'],
                'who_text' => $data['who_text'],
                'who_extra' => $data['who_extra'] ?? null,
                'directions_title' => $data['directions_title'],
                'cta_title' => $data['cta_title'] ?? null,
                'cta_text' => $data['cta_text'] ?? null,
                'cta_button_label' => $data['cta_button_label'] ?? null,
                'cta_button_url' => $data['cta_button_url'] ?? null,
                'seo_title' => $data['seo_title'] ?? null,
                'seo_description' => $data['seo_description'] ?? null,
            ]);

            SiteContent::ensureOptions()->update([
                'logo_text' => $logo,
            ]);

            Service::syncCards($directions);
        });
    }
}
