<?php

namespace Celios\Core\Filament\Resources\Brands\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        $availableLocales = config('locales.available', [
            'sr' => 'Srpski',
            'en' => 'English',
            'it' => 'Italiano',
        ]);

        return $schema
            ->columns(1)
            ->components([
                Tabs::make('brand_tabs')
                    ->tabs([
                        // Tab 1: Identity & Domains
                        Tab::make('tab_identity')
                            ->label(fn () => __('brands.tab_identity'))
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Section::make(fn () => __('brands.identity_section'))
                                    ->description(fn () => __('brands.identity_desc'))
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('name')
                                                ->label(fn () => __('brands.name'))
                                                ->required()
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(function ($state, callable $set, string $context) {
                                                    if ($context === 'create') {
                                                        $set('slug', str($state)->slug());
                                                    }
                                                }),

                                            TextInput::make('slug')
                                                ->label(fn () => __('brands.slug'))
                                                ->required()
                                                ->unique(table: 'brands', column: 'slug', ignoreRecord: true),

                                            TextInput::make('headline')
                                                ->label(fn () => __('brands.headline'))
                                                ->placeholder('e.g. Modern Web & Digital Platform'),

                                            TextInput::make('primary_domain')
                                                ->label(fn () => __('brands.primary_domain'))
                                                ->helperText(fn () => __('brands.primary_domain_helper'))
                                                ->placeholder('e.g. celios.io or localhost'),
                                        ]),

                                        TagsInput::make('domains')
                                            ->label(fn () => __('brands.domains'))
                                            ->helperText(fn () => __('brands.domains_helper'))
                                            ->placeholder('Add domain and press Enter'),

                                        Textarea::make('description')
                                            ->label(fn () => __('brands.description'))
                                            ->rows(3),

                                        TextInput::make('copyright_text')
                                            ->label(fn () => __('brands.copyright_text'))
                                            ->placeholder('e.g. © 2026 Celios CMS. All rights reserved.'),

                                        Grid::make(2)->schema([
                                            Toggle::make('is_default')
                                                ->label(fn () => __('brands.is_default'))
                                                ->helperText(fn () => __('brands.is_default_helper'))
                                                ->default(false),

                                            Toggle::make('is_active')
                                                ->label(fn () => __('brands.is_active'))
                                                ->default(true),
                                        ]),
                                    ]),
                            ]),

                        // Tab 2: Logos & Media
                        Tab::make('tab_media')
                            ->label(fn () => __('brands.tab_media'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make(fn () => __('brands.media_section'))
                                    ->description(fn () => __('brands.media_desc'))
                                    ->schema([
                                        Grid::make(3)->schema([
                                            CuratorPicker::make('logo_light_id')
                                                ->label(fn () => __('brands.logo_light'))
                                                ->buttonLabel(fn () => __('fields.choose_picture') ?? 'Select File')
                                                ->directory('brands')
                                                ->nullable(),

                                            CuratorPicker::make('logo_dark_id')
                                                ->label(fn () => __('brands.logo_dark'))
                                                ->buttonLabel(fn () => __('fields.choose_picture') ?? 'Select File')
                                                ->directory('brands')
                                                ->nullable(),

                                            CuratorPicker::make('favicon_id')
                                                ->label(fn () => __('brands.favicon'))
                                                ->buttonLabel(fn () => __('fields.choose_picture') ?? 'Select File')
                                                ->directory('brands')
                                                ->nullable(),
                                        ]),
                                    ]),
                            ]),

                        // Tab 3: Contact & Legal
                        Tab::make('tab_contact')
                            ->label(fn () => __('brands.tab_contact'))
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make(fn () => __('brands.contact_section'))
                                    ->description(fn () => __('brands.contact_desc'))
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('company_name')
                                                ->label(fn () => __('brands.company_name')),

                                            TextInput::make('tax_id')
                                                ->label(fn () => __('brands.tax_id')),

                                            TextInput::make('email')
                                                ->label(fn () => __('brands.email'))
                                                ->email(),

                                            TextInput::make('phone')
                                                ->label(fn () => __('brands.phone'))
                                                ->tel(),

                                            TextInput::make('address')
                                                ->label(fn () => __('brands.address')),

                                            TextInput::make('city')
                                                ->label(fn () => __('brands.city')),

                                            TextInput::make('postal_code')
                                                ->label(fn () => __('brands.postal_code')),

                                            TextInput::make('country')
                                                ->label(fn () => __('brands.country')),
                                        ]),

                                        KeyValue::make('social_links')
                                            ->label(fn () => __('brands.social_links'))
                                            ->keyLabel('Platform (e.g. instagram, facebook, linkedin, x, youtube)')
                                            ->valueLabel('Profile URL'),
                                    ]),
                            ]),

                        // Tab 4: Languages & Currencies
                        Tab::make('tab_localization')
                            ->label(fn () => __('brands.tab_localization'))
                            ->icon('heroicon-o-language')
                            ->schema([
                                Section::make(fn () => __('brands.localization_section'))
                                    ->description(fn () => __('brands.localization_desc'))
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Select::make('default_locale')
                                                ->label(fn () => __('brands.default_locale'))
                                                ->options($availableLocales)
                                                ->default(config('locales.default', 'sr'))
                                                ->required(),

                                            CheckboxList::make('supported_locales')
                                                ->label(fn () => __('brands.supported_locales'))
                                                ->options($availableLocales)
                                                ->default(array_keys($availableLocales)),
                                        ]),

                                        Grid::make(3)->schema([
                                            Select::make('default_currency')
                                                ->label(fn () => __('brands.default_currency'))
                                                ->options([
                                                    'EUR' => 'EUR (€)',
                                                    'USD' => 'USD ($)',
                                                    'RSD' => 'RSD (дин)',
                                                    'GBP' => 'GBP (£)',
                                                    'CHF' => 'CHF',
                                                ])
                                                ->default('EUR')
                                                ->required(),

                                            TagsInput::make('supported_currencies')
                                                ->label(fn () => __('brands.supported_currencies'))
                                                ->placeholder('Add code (e.g. EUR, USD, RSD)')
                                                ->default(['EUR', 'RSD']),

                                            Radio::make('currency_symbol_position')
                                                ->label(fn () => __('brands.currency_symbol_position'))
                                                ->options(fn () => [
                                                    'before' => __('brands.currency_before'),
                                                    'after' => __('brands.currency_after'),
                                                ])
                                                ->default('after')
                                                ->inline(false),
                                        ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
