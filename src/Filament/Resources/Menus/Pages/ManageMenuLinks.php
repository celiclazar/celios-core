<?php

namespace Celios\Core\Filament\Resources\Menus\Pages;

use Celios\Core\Filament\Resources\Menus\MenuResource;
use Celios\Core\Models\Category as BlogCategory;
use Celios\Core\Models\MenuItem;
use Celios\Core\Models\Page as CmsPage;
use Celios\Core\Models\Post as BlogPost;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManageMenuLinks extends ManageRelatedRecords
{
    protected static string $resource = MenuResource::class;

    protected static string $relationship = 'menuItems';

    public function getTitle(): string
    {
        return __('menus.manage_links_for', [
            'menu' => $this->getRecord()->name,
        ]);
    }

    public function form(Schema $schema): Schema
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
                                    ->map(function (string $label, string $locale) {
                                        return Tabs\Tab::make(strtoupper($locale) . " ({$label})")
                                            ->id($locale)
                                            ->schema([
                                                TextInput::make("title.{$locale}")
                                                    ->label(__('fields.link_title') . ' (' . strtoupper($locale) . ')')
                                                    ->placeholder(__('fields.link_title'))
                                                    ->required($locale === config('locales.default', 'sr')),

                                                TextInput::make("custom_url.{$locale}")
                                                    ->label(__('fields.custom_url') . ' (' . strtoupper($locale) . ')')
                                                    ->placeholder('https://...'),
                                            ]);
                                    })
                                    ->values()
                                    ->all()
                            ),
                    ]),

                Section::make(__('fields.target_module') ?? 'Link Destination & Settings')
                    ->icon('heroicon-o-link')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('parent_id')
                            ->label(__('fields.parent_item'))
                            ->placeholder(__('fields.root_level'))
                            ->options(function (?MenuItem $record): array {
                                $locale = app()->getLocale();
                                $menu = $this->getRecord();

                                if (! $menu) {
                                    return [];
                                }

                                return $menu->menuItems()
                                    ->when($record?->exists, fn ($query) => $query->where('id', '!=', $record->id))
                                    ->whereNull('parent_id')
                                    ->with('linkable')
                                    ->get()
                                    ->mapWithKeys(function (MenuItem $item) use ($locale) {
                                        $title = $item->getTranslation('title', $locale, false);

                                        if (blank($title) && $item->linkable) {
                                            $title = $item->linkable->getTranslation('title', $locale, false);
                                        }

                                        if (blank($title)) {
                                            $defaultLocale = config('locales.default', 'sr');
                                            $title = $item->getTranslation('title', $defaultLocale, false);
                                        }

                                        if (blank($title)) {
                                            $allTitles = $item->getTranslations('title');
                                            $title = !empty($allTitles) ? reset($allTitles) : null;
                                        }

                                        return [
                                            $item->id => $title ?: (__('fields.no_title') . " (#{$item->id})"),
                                        ];
                                    })
                                    ->all();
                            })
                            ->preload()
                            ->searchable()
                            ->nullable()
                            ->helperText('Select a top-level link from this menu to nest this item under it, or leave empty for a root link.'),

                        Select::make('linkable_type')
                            ->label(__('fields.target_module') ?? 'Target Type')
                            ->placeholder('Custom URL / External Link')
                            ->options([
                                CmsPage::class => __('sidebar.pages') . ' (CMS Page)',
                                BlogCategory::class => __('sidebar.categories') . ' (Blog Category)',
                                BlogPost::class => __('sidebar.posts') . ' (Blog Post)',
                            ])
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('linkable_id', null)),

                        Select::make('linkable_id')
                            ->label(__('fields.cms_page_target') ?? 'Target Item')
                            ->placeholder('Select target item...')
                            ->visible(fn (callable $get) => filled($get('linkable_type')))
                            ->options(function (callable $get): array {
                                $type = $get('linkable_type');
                                $locale = app()->getLocale();

                                if ($type === CmsPage::class) {
                                    return CmsPage::query()->get()->mapWithKeys(function (CmsPage $p) use ($locale) {
                                        $title = $p->getTranslation('title', $locale, true) ?: __('fields.no_title');
                                        if ($p->isSystem()) {
                                            $title .= ' [' . $p->type->label() . ']';
                                        }
                                        return [$p->id => $title];
                                    })->all();
                                }

                                if ($type === BlogCategory::class) {
                                    return BlogCategory::query()->get()->mapWithKeys(fn (BlogCategory $c) => [
                                        $c->id => $c->getTranslation('title', $locale, true) ?: __('fields.no_title'),
                                    ])->all();
                                }

                                if ($type === BlogPost::class) {
                                    return BlogPost::query()->published()->get()->mapWithKeys(fn (BlogPost $p) => [
                                        $p->id => $p->getTranslation('title', $locale, true) ?: __('fields.no_title'),
                                    ])->all();
                                }

                                return [];
                            })
                            ->searchable()
                            ->preload(),

                        Select::make('target')
                            ->label(__('fields.link_opening'))
                            ->options([
                                '_self' => __('fields.target_self'),
                                '_blank' => __('fields.target_blank'),
                            ])
                            ->default('_self'),

                        Toggle::make('is_visible')
                            ->label(__('fields.visible_on_site'))
                            ->default(true),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn (MenuItem $record): string => (
            $record->getTranslation('title', app()->getLocale(), true)
                ?: __('fields.no_title')
            ))
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->columns([
                TextColumn::make('title')
                    ->label(__('fields.title'))
                    ->state(function (MenuItem $record): string {
                        $title = $record->getTranslation('title', app()->getLocale(), true) ?: __('fields.no_title');
                        return $record->parent_id ? "↳ {$title}" : $title;
                    })
                    ->searchable(),

                TextColumn::make('parent.title')
                    ->label(__('fields.parent_item'))
                    ->state(fn (MenuItem $record): string => $record->parent
                        ? ($record->parent->getTranslation('title', app()->getLocale(), true) ?: __('fields.no_title'))
                        : __('fields.root_level')
                    )
                    ->badge()
                    ->color(fn (MenuItem $record): string => $record->parent ? 'info' : 'gray'),

                IconColumn::make('is_visible')
                    ->label(__('fields.visible'))
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('actions.add_new_item'))
                    ->modalWidth(Width::TwoExtraLarge)
                    ->modalHeading(__('actions.add_new_item'))
                    ->slideOver(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label(__('actions.edit'))
                    ->modalWidth(Width::TwoExtraLarge)
                    ->modalHeading(__('actions.edit'))
                    ->slideOver(),

                DeleteAction::make()
                    ->label(__('actions.delete')),
            ]);
    }
}
