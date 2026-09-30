<?php

namespace Celios\Core\Filament\Resources\Documents\Schemas;

use Celios\Core\Enums\DocumentAccessLevel;
use Celios\Core\Models\Document;
use Celios\Core\Models\DocumentCategory;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('fields.translations'))
                    ->icon('heroicon-o-language')
                    ->columnSpanFull()
                    ->schema([
                        Tabs::make('translations_tabs')
                            ->tabs(
                                collect(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']))
                                    ->map(function (string $label, string $lang) {
                                        return Tabs\Tab::make(strtoupper($lang) . " ({$label})")
                                            ->id($lang)
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        TextInput::make("title.{$lang}")
                                                            ->label(__('fields.title') . ' (' . strtoupper($lang) . ')')
                                                            ->required($lang === config('locales.default', 'sr'))
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function (string $operation, $state, callable $set, callable $get) use ($lang) {
                                                                if ($operation === 'create' || blank($get("slug.{$lang}"))) {
                                                                    $set("slug.{$lang}", (string) str($state)->slug());
                                                                }
                                                            }),

                                                        TextInput::make("slug.{$lang}")
                                                            ->label(__('fields.url_slug') . ' (' . strtoupper($lang) . ')')
                                                            ->required($lang === config('locales.default', 'sr'))
                                                            ->dehydrated()
                                                            ->unique(
                                                                table: 'documents',
                                                                column: "slug->{$lang}",
                                                                ignoreRecord: true
                                                            ),
                                                    ]),

                                                Textarea::make("description.{$lang}")
                                                    ->label(__('fields.description') . ' (' . strtoupper($lang) . ')')
                                                    ->rows(3),
                                            ]);
                                    })
                                    ->values()
                                    ->all()
                            ),
                    ]),

                Section::make(__('documents.file'))
                    ->icon('heroicon-o-paper-clip')
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('file_path')
                            ->label(__('documents.file'))
                            ->disk('documents')
                            ->directory('files')
                            ->visibility('private')
                            ->preserveFilenames()
                            ->storeFileNamesIn('file_name')
                            ->required(fn (?Document $record) => $record === null)
                            ->maxSize(51200) // 50MB
                            ->helperText(__('documents.file_helper'))
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('file_name')
                                    ->label(__('documents.file_name'))
                                    ->nullable(),

                                TextInput::make('file_type')
                                    ->label(__('documents.file_type'))
                                    ->placeholder('e.g. pdf, docx, xlsx')
                                    ->nullable(),

                                TextInput::make('version')
                                    ->label(__('documents.version'))
                                    ->placeholder(__('documents.version_placeholder')),
                            ]),
                    ]),

                Section::make(__('fields.general_settings'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('category_id')
                                    ->label(__('documents.category'))
                                    ->options(function (): array {
                                        return DocumentCategory::query()
                                            ->get()
                                            ->mapWithKeys(fn (DocumentCategory $cat) => [
                                                $cat->id => ($cat->is_internal ? '[🔒 ' . __('documents.access_internal') . '] ' : '') . $cat->getLocalizedTitle(),
                                            ])
                                            ->all();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),

                                Select::make('access_level')
                                    ->label(__('documents.access_level'))
                                    ->helperText(__('documents.access_level_helper'))
                                    ->options(collect(DocumentAccessLevel::cases())->mapWithKeys(fn (DocumentAccessLevel $level) => [
                                        $level->value => $level->label(),
                                    ]))
                                    ->default(DocumentAccessLevel::PUBLIC->value)
                                    ->required()
                                    ->live(),

                                Select::make('allowed_roles')
                                    ->label(__('documents.allowed_roles'))
                                    ->helperText(__('documents.allowed_roles_helper'))
                                    ->options(function (): array {
                                        return Role::query()->pluck('name', 'name')->all();
                                    })
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn (callable $get) => $get('access_level') === DocumentAccessLevel::ROLES->value)
                                    ->columnSpanFull(),

                                DateTimePicker::make('published_at')
                                    ->label(__('fields.published_at'))
                                    ->default(now()),

                                Toggle::make('is_published')
                                    ->label(__('fields.is_published'))
                                    ->default(true)
                                    ->inline(false),
                            ]),
                    ]),
            ]);
    }
}
