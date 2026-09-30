<?php

namespace Celios\Core\Filament\Resources\Categories\Schemas;

use Celios\Core\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class CategoryForm
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
                                                            ->afterStateUpdated(function ($state, callable $set) use ($lang) {
                                                                $set("slug.{$lang}", str($state)->slug());
                                                            }),

                                                        TextInput::make("slug.{$lang}")
                                                            ->label(__('fields.url_slug') . ' (' . strtoupper($lang) . ')')
                                                            ->required($lang === config('locales.default', 'sr'))
                                                            ->unique(
                                                                table: 'categories',
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

                Section::make(__('fields.general_settings') ?? 'Category Settings')
                    ->icon('heroicon-o-folder')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('parent_id')
                                    ->label(__('fields.parent_item'))
                                    ->placeholder(__('fields.root_level'))
                                    ->options(function (?Category $record): array {
                                        $locale = app()->getLocale();

                                        return Category::query()
                                            ->when($record?->exists, fn ($query) => $query->where('id', '!=', $record->id))
                                            ->whereNull('parent_id')
                                            ->get()
                                            ->mapWithKeys(fn (Category $cat) => [
                                                $cat->id => $cat->getTranslation('title', $locale, true) ?: __('fields.no_title'),
                                            ])
                                            ->all();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),

                                Toggle::make('is_visible')
                                    ->label(__('fields.visible_on_site'))
                                    ->default(true)
                                    ->inline(false),
                            ]),
                    ]),
            ]);
    }
}
