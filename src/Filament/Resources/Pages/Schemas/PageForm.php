<?php

namespace Celios\Core\Filament\Resources\Pages\Schemas;

use Celios\Core\Enums\PageType;
use Celios\Core\Models\Page;
use Celios\Core\Services\ModuleManager;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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

                Tabs::make(fn () => __('fields.title_localization'))
                    ->tabs([
                        self::getLocaleTab('sr', 'Srpski'),
                        self::getLocaleTab('en', 'English'),
                        self::getLocaleTab('it', 'Italiano'),
                    ])->columnSpanFull(),

                Builder::make('draft_content')
                    ->label(fn () => __('fields.page_content_blocks'))
                    ->blocks(\Celios\Core\Services\ModuleManager::getAvailablePageBlocks())
                    ->cloneable()
                    ->collapsed()
                    ->blockIcons()
                    ->blockPickerColumns(['default' => 1, 'sm' => 2, 'lg' => 3])
                    ->columnSpanFull()

            ]);
    }

    protected static function getLocaleTab(string $lang, string $label): Tabs\Tab
    {
        return Tabs\Tab::make($label)
            ->schema([
                TextInput::make("title.{$lang}")
                    ->label(fn () => __('fields.page_title') . ' (' . strtoupper($lang) . ')')
                    ->required($lang === 'sr')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($lang) {
                        $typeVal = $get('type');
                        $pageType = $typeVal instanceof PageType ? $typeVal : PageType::tryFrom((string) $typeVal);
                        if (! $pageType || ! $pageType->isSystem()) {
                            $set("slug.{$lang}", str($state)->slug());
                        }
                    }),

                TextInput::make("slug.{$lang}")
                    ->label(fn () => __('fields.url_slug') . ' (' . strtoupper($lang) . ')')
                    ->required($lang === 'sr')
                    ->disabled(function (callable $get) {
                        $typeVal = $get('type');
                        $pageType = $typeVal instanceof PageType ? $typeVal : PageType::tryFrom((string) $typeVal);
                        return $pageType?->isSystem() ?? false;
                    })
                    ->dehydrated()
                    ->unique(
                        table: 'pages',
                        column: "slug->{$lang}",
                        ignoreRecord: true
                    )
                    ->helperText(function (callable $get) {
                        $typeVal = $get('type');
                        $pageType = $typeVal instanceof PageType ? $typeVal : PageType::tryFrom((string) $typeVal);
                        if ($pageType && $pageType->isSystem()) {
                            return __('fields.system_page_slug_locked');
                        }
                        return __('fields.slug_helper');
                    }),
            ]);
    }
}
