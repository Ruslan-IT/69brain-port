<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiryResource\Pages\EditInquiry;
use App\Filament\Resources\InquiryResource\Pages\ListInquiries;
use App\Models\Inquiry;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema as SchemaFacade;

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;

    protected static ?string $slug = 'zayavki';

    protected static ?string $navigationLabel = 'Заявки';

    protected static ?string $modelLabel = 'заявка';

    protected static ?string $pluralModelLabel = 'Заявки';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        if (! SchemaFacade::hasTable('inquiries')) {
            return null;
        }

        $count = Inquiry::query()->where('status', Inquiry::STATUS_NEW)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Обращение')
                ->schema([
                    Placeholder::make('received_at')
                        ->label('Поступила')
                        ->content(fn (Inquiry $record): string => $record->created_at?->timezone(config('app.timezone'))->format('d.m.Y H:i') ?? ''),
                    Select::make('status')
                        ->label('Статус')
                        ->options(Inquiry::statusOptions())
                        ->required()
                        ->native(false),
                    TextInput::make('name')
                        ->label('Имя')
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('phone')
                        ->label('Телефон')
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('email')
                        ->label('Электронная почта')
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('page_url')
                        ->label('Страница')
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),
                    Textarea::make('message')
                        ->label('Сообщение')
                        ->disabled()
                        ->dehydrated(false)
                        ->rows(8)
                        ->columnSpanFull(),
                    Placeholder::make('mail_state')
                        ->label('Письмо')
                        ->content(function (Inquiry $record): string {
                            if ($record->mail_sent_at) {
                                return 'Отправлено '.$record->mail_sent_at->timezone(config('app.timezone'))->format('d.m.Y H:i');
                            }

                            return filled($record->mail_error)
                                ? 'Не отправлено: '.$record->mail_error
                                : 'Не отправлено';
                        })
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('email')
                    ->label('Почта')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Inquiry::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Inquiry::STATUS_NEW => 'warning',
                        Inquiry::STATUS_IN_PROGRESS => 'info',
                        Inquiry::STATUS_PROCESSED => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options(Inquiry::statusOptions()),
                Filter::make('created_at')
                    ->label('Дата')
                    ->schema([
                        DatePicker::make('from')
                            ->label('С даты'),
                        DatePicker::make('until')
                            ->label('По дату'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make()
                    ->label('Открыть'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInquiries::route('/'),
            'edit' => EditInquiry::route('/{record}/edit'),
        ];
    }
}
