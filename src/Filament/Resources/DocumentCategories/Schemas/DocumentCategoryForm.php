<?php

namespace Celios\Core\Filament\Resources\DocumentCategories\Schemas;

use Celios\Core\Models\DocumentCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class DocumentCategoryForm
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
                                                                table: 'document_categories',
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

                Section::make(__('fields.general_settings'))
                    ->icon('heroicon-o-folder')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('parent_id')
                                    ->label(__('fields.parent_item'))
                                    ->placeholder(__('fields.root_level'))
                                    ->options(function (?DocumentCategory $record): array {
                                        return DocumentCategory::query()
                                            ->when($record?->exists, fn ($query) => $query->where('id', '!=', $record->id))
                                            ->whereNull('parent_id')
                                            ->get()
                                            ->mapWithKeys(fn (DocumentCategory $cat) => [
                                                $cat->id => $cat->getLocalizedTitle(),
                                            ])
                                            ->all();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),

                                TextInput::make('order')
                                    ->label(__('fields.order'))
                                    ->numeric()
                                    ->default(0),

                                Toggle::make('is_active')
                                    ->label(__('fields.active'))
                                    ->default(true)
                                    ->inline(false),

                                Toggle::make('is_internal')
                                    ->label(__('documents.is_internal'))
                                    ->helperText(__('documents.is_internal_helper'))
                                    ->default(false)
                                    ->inline(false),
                            ]),
                    ]),
            ]);
    }
}
