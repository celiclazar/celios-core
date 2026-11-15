<?php

namespace Celios\Core\Filament\Resources\Pages\Schemas;

use Celios\Core\Enums\PageType;
use Celios\Core\Models\Page;
use Celios\Core\Services\ModuleManager;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label(fn () => __('fields.page_type'))
                    ->options(collect(PageType::cases())->mapWithKeys(fn (PageType $type) => [
                        $type->value => $type->label(),
                    ]))
                    ->default(PageType::STANDARD->value)
                    ->required()
                    ->visible(fn (string $operation) => $operation === 'create')
                    ->live()
                    ->disableOptionWhen(function (string $value, ?Page $record) {
                        if ($value === PageType::STANDARD->value) {
                            return false;
                        }

                        return Page::where('type', $value)
                            ->when($record?->exists, fn ($q) => $q->where('id', '!=', $record->id))
                            ->exists();
                    })
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $typeVal = $state instanceof PageType ? $state->value : $state;
                        $pageType = PageType::tryFrom((string) $typeVal);

                        if ($pageType && $pageType->isSystem()) {
                            foreach ($pageType->standardSlugs() as $lang => $stdSlug) {
                                $set("slug.{$lang}", $stdSlug);
                            }
                            foreach ($pageType->defaultTitles() as $lang => $defTitle) {
                                if (empty($get("title.{$lang}"))) {
                                    $set("title.{$lang}", $defTitle);
                                }
                            }
                        }
                    }),

                Toggle::make('is_visible')
                    ->label(fn () => __('fields.visible_on_site'))
                    ->default(true),

                Section::make(__('fields.translations'))
                    ->icon('heroicon-o-language')
                    ->columnSpanFull()
                    ->schema([
                        Tabs::make('translations_tabs')
                            ->tabs(
                                collect(active_locales())
                                    ->map(fn (string $label, string $lang) => self::getLocaleTab($lang, $label))
                                    ->values()
                                    ->all()
                            ),
                    ]),

                Builder::make('draft_content')
                    ->label(fn () => __('fields.page_content_blocks'))
                    ->blocks(ModuleManager::getAvailablePageBlocks())
                    ->cloneable()
                    ->collapsed()
                    ->blockIcons()
                    ->blockPickerColumns(['default' => 1, 'sm' => 2, 'lg' => 3])
                    ->columnSpanFull(),
            ]);
    }

    protected static function getLocaleTab(string $lang, string $label): Tabs\Tab
    {
        return Tabs\Tab::make(strtoupper($lang)." ({$label})")
            ->id($lang)
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextInput::make("title.{$lang}")
                            ->label(__('fields.title').' ('.strtoupper($lang).')')
                            ->required($lang === config('locales.default', 'sr'))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, callable $get, ?Page $record, string $operation) use ($lang) {
                                $pageType = $record?->type ?? (PageType::tryFrom((string) $get('type')));
                                if ($operation === 'create' && (! $pageType || ! $pageType->isSystem())) {
                                    $set("slug.{$lang}", str($state)->slug());
                                }
                            }),

                        TextInput::make("slug.{$lang}")
                            ->label(__('fields.url_slug').' ('.strtoupper($lang).')')
                            ->required($lang === config('locales.default', 'sr'))
                            ->disabled(function (?Page $record, callable $get) {
                                $pageType = $record?->type ?? (PageType::tryFrom((string) $get('type')));

                                return $pageType?->isSystem() ?? false;
                            })
                            ->dehydrated()
                            ->unique(
                                table: 'pages',
                                column: "slug->{$lang}",
                                ignoreRecord: true
                            )
                            ->helperText(function (?Page $record, callable $get) {
                                $pageType = $record?->type ?? (PageType::tryFrom((string) $get('type')));
                                if ($pageType && $pageType->isSystem()) {
                                    return __('fields.system_page_slug_locked');
                                }

                                return __('fields.slug_helper');
                            }),
                    ]),
            ]);
    }
}
