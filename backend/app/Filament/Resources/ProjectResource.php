<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use App\Services\GithubRepositoryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket-square';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?string $navigationLabel = 'Проекты';

    protected const LOCALE_LABELS = [
        'ru' => 'Русский',
        'en' => 'English',
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Контент')
                    ->schema([
                        Forms\Components\Tabs::make('translations')
                            ->label('Локализации')
                            ->tabs(self::buildLocaleTabs())
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('slug')
                            ->label('Слаг')
                            ->required()
                            ->alphaDash()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true, table: Project::class),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Медиа')
                    ->schema([
                        Forms\Components\FileUpload::make('preview_image_path')
                            ->label('Обложка')
                            ->image()
                            ->disk('public')
                            ->directory('projects/previews')
                            ->imagePreviewHeight('200')
                            ->maxSize(4096)
                            ->helperText('Загрузите изображение, которое будет отображаться в карточке проекта.'),
                        Forms\Components\FileUpload::make('gallery_images')
                            ->label('Галерея')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->panelLayout('grid')
                            ->disk('public')
                            ->directory('projects/gallery')
                            ->maxSize(4096)
                            ->maxFiles(12)
                            ->helperText('Прикрепите дополнительные скриншоты или медиа (до 12 файлов).'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Репозиторий')
                    ->schema([
                        Forms\Components\TextInput::make('repo_owner')
                            ->label('Владелец')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('repo_name')
                            ->label('Репозиторий')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('default_branch')
                            ->label('Ветка по умолчанию')
                            ->default('main')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TagsInput::make('tags')
                            ->placeholder('Добавьте тег')
                            ->label('Теги')
                            ->helperText('Например: Laravel, Livewire, Tailwind'),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('В избранном')
                            ->default(false),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Опубликован')
                            ->default(true),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Синхронизация с GitHub')
                    ->description('Данные только для чтения, полученные во время последней синхронизации.')
                    ->schema([
                        Forms\Components\Placeholder::make('synced_at')
                            ->label('Последняя синхронизация')
                            ->content(fn (?Project $record): string => $record?->synced_at?->diffForHumans() ?? 'Не выполнялась')
                            ->columnSpan(1),
                        Forms\Components\Placeholder::make('last_commit_at')
                            ->label('Последний коммит')
                            ->content(fn (?Project $record): string => $record?->last_commit_at?->diffForHumans() ?? 'Нет данных')
                            ->columnSpan(1),
                        Forms\Components\Placeholder::make('languages_display')
                            ->label('Языки')
                            ->content(fn (?Project $record): string => $record && ! empty($record->languages)
                                ? collect($record->languages)->map(fn ($bytes, $language) => $language)->join(', ')
                                : '—'),
                        Forms\Components\Placeholder::make('stars')
                            ->label('Звёзды')
                            ->content(fn (?Project $record): string => (string) (data_get($record?->github_meta, 'stargazers') ?? '—')),
                        Forms\Components\Placeholder::make('forks')
                            ->label('Форки')
                            ->content(fn (?Project $record): string => (string) (data_get($record?->github_meta, 'forks') ?? '—')),
                        Forms\Components\Placeholder::make('open_issues')
                            ->label('Открытые issues')
                            ->content(fn (?Project $record): string => (string) (data_get($record?->github_meta, 'open_issues') ?? '—')),
                        Forms\Components\Placeholder::make('topics')
                            ->label('Темы')
                            ->content(fn (?Project $record): string => collect(data_get($record?->github_meta, 'topics', []))->join(', ') ?: '—'),
                        Forms\Components\Placeholder::make('license')
                            ->label('Лицензия')
                            ->content(fn (?Project $record): string => (string) (data_get($record?->github_meta, 'license') ?? '—')),
                        Forms\Components\Placeholder::make('homepage')
                            ->label('Домашняя страница')
                            ->content(fn (?Project $record): string => (string) (data_get($record?->github_meta, 'homepage') ?? '—')),
                        Forms\Components\Placeholder::make('last_commit_sha')
                            ->label('SHA последнего коммита')
                            ->content(fn (?Project $record): string => (string) (data_get($record?->github_meta, 'last_commit_sha') ?? '—')),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('preview_image_path')
                    ->label('Обложка')
                    ->disk('public')
                    ->square()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->state(fn (Project $record): string => $record->getTranslation('title', 'ru')
                        ?? $record->getTranslation('title', Project::DEFAULT_LOCALE)
                        ?? '-')
                    ->searchable(
                        query: function (Builder $query, string $search): Builder {
                            $pattern = "%{$search}%";

                            return $query->where(function (Builder $subQuery) use ($pattern) {
                                $subQuery
                                    ->where('title_translations->ru', 'like', $pattern)
                                    ->orWhere('title_translations->en', 'like', $pattern);
                            });
                        }
                    )
                    ->sortable(
                        query: fn (Builder $query, string $direction): Builder => $query->orderBy(
                            'title_translations->' . Project::DEFAULT_LOCALE,
                            $direction
                        )
                    ),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Слаг')
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('repository')
                    ->label('Репозиторий')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->label('Опубликован'),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('В избранном'),
                Tables\Columns\TextColumn::make('github_meta.stargazers')
                    ->label('Звёзды')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('github_meta.forks')
                    ->label('Форки')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('last_commit_at')
                    ->label('Последний коммит')
                    ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Обновлён'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Редактировать'),
                self::syncGitHubAction(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить выделенные'),
                    self::syncGitHubBulkAction(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('is_featured')
            ->orderBy('sort_order');
    }

    protected static function syncGitHubAction(): Action
    {
        return Action::make('syncGithub')
            ->label('Синхронизировать GitHub')
            ->icon('heroicon-o-arrow-path')
            ->requiresConfirmation()
            ->action(function (Project $record): void {
                self::syncRecord($record);
            })
            ->successNotificationTitle('Данные GitHub обновлены.');
    }

    protected static function syncGitHubBulkAction(): BulkAction
    {
        return BulkAction::make('syncGitHub')
            ->label('Синхронизировать GitHub')
            ->icon('heroicon-o-arrow-path')
            ->requiresConfirmation()
            ->action(function (Collection $records) {
                $records->each(fn (Project $project) => self::syncRecord($project));
            })
            ->successNotificationTitle('Выбранные проекты обновлены.');
    }

    protected static function syncRecord(Project $project): void
    {
        $service = app(GithubRepositoryService::class);
        $service->clearCache($project);
        $service->syncProject($project);
    }

    protected static function buildLocaleTabs(): array
    {
        return collect(Project::AVAILABLE_LOCALES)
            ->map(function (string $locale) {
                $label = self::LOCALE_LABELS[$locale] ?? strtoupper($locale);

                return Tab::make($label)
                    ->schema([
                        Forms\Components\TextInput::make("title_translations.$locale")
                            ->label('Название')
                            ->required($locale === Project::DEFAULT_LOCALE)
                            ->maxLength(255),
                        Forms\Components\Textarea::make("summary_translations.$locale")
                            ->label('Краткое описание')
                            ->rows(4),
                    ]);
            })
            ->values()
            ->all();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
