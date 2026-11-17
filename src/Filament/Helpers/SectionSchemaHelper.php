<?php

namespace Celios\Core\Filament\Helpers;

use Celios\Core\Filament\Blocks\ButtonBlock;
use Celios\Core\Filament\Blocks\CTABlock;
use Celios\Core\Filament\Blocks\FAQBlock;
use Celios\Core\Filament\Blocks\FeaturesBlock;
use Celios\Core\Filament\Blocks\FormBlock;
use Celios\Core\Filament\Blocks\GalleryBlock;
use Celios\Core\Filament\Blocks\ImageBlock;
use Celios\Core\Filament\Blocks\NewsletterBlock;
use Celios\Core\Filament\Blocks\StatsBlock;
use Celios\Core\Filament\Blocks\TextBlock;
use Celios\Core\Services\ModuleManager;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section as FormSection;
use Filament\Schemas\Components\Tabs;

class SectionSchemaHelper
{
    /**
     * Custom blocks registered for section columns.
     */
    protected static array $customColumnBlocks = [];

    public static function registerColumnBlock(\Filament\Forms\Components\Builder\Block $block): void
    {
        static::$customColumnBlocks[] = $block;
    }

    public static function registerColumnBlocks(array $blocks): void
    {
        foreach ($blocks as $block) {
            static::registerColumnBlock($block);
        }
    }

    /**
     * Blocks enabled inside section columns.
     */
    public static function getColumnBlocks(): array
    {
        $blocks = [
            TextBlock::make(),
            ButtonBlock::make(),
            ImageBlock::make(),
            GalleryBlock::make(),
            CTABlock::make(),
            FAQBlock::make(),
            FeaturesBlock::make(),
            StatsBlock::make(),
        ];

        if (ModuleManager::isEnabled('forms') && class_exists(FormBlock::class)) {
            $blocks[] = FormBlock::make();
        }

        if (ModuleManager::isEnabled('newsletter') && class_exists(NewsletterBlock::class)) {
            $blocks[] = NewsletterBlock::make();
        }

        return array_merge($blocks, static::$customColumnBlocks);
    }

    /**
     * Shared schema for Section styling, layout, header, and column blocks.
     */
    public static function getSchema(bool $includeNameFields = false): array
    {
        $components = [];

        if ($includeNameFields) {
            $components[] = FormSection::make('Osnovni podaci sekcije')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('name')
                            ->label('Naziv sekcije (Interni)')
                            ->placeholder('npr. 2 Kolone Tekst + Dugme')
                            ->required(),

                        TextInput::make('slug')
                            ->label('Slug / Identifikator')
                            ->placeholder('npr. homepage-services-split')
                            ->unique(table: 'sections', column: 'slug', ignoreRecord: true),

                        Select::make('category')
                            ->label('Kategorija')
                            ->options([
                                'content' => 'Tekst & Sadržaj (Content)',
                                'features' => 'Prednosti & Usluge (Features)',
                                'cta' => 'Poziv na akciju (CTA)',
                                'headers' => 'Zaglavlja & Uvod (Headers)',
                                'custom' => 'Prilagođeno (Custom)',
                            ])
                            ->default('content'),
                    ]),
                ]);
        }

        $components[] = Tabs::make('section_configuration')
            ->tabs([
                Tabs\Tab::make('Raspored & Kolone')
                    ->icon('heroicon-o-squares-2x2')
                    ->schema([
                        Select::make('layout')
                            ->label('Raspored kolona / Layout')
                            ->options([
                                '1_col' => '1 Kolona (Puna širina / Full Width)',
                                '2_cols' => '2 Kolone (50% / 50%)',
                                '3_cols' => '3 Kolone (33% / 33% / 33%)',
                                '4_cols' => '4 Kolone (25% / 25% / 25% / 25%)',
                                '1_2_cols' => '2 Kolone asimetrično (33% levo / 67% desno)',
                                '2_1_cols' => '2 Kolone asimetrično (67% levo / 33% desno)',
                            ])
                            ->default('2_cols')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $cols = $get('columns') ?? [];
                                $needed = match ($state) {
                                    '1_col' => 1,
                                    '3_cols' => 3,
                                    '4_cols' => 4,
                                    default => 2,
                                };
                                if (count($cols) < $needed) {
                                    while (count($cols) < $needed) {
                                        $cols[] = ['blocks' => []];
                                    }
                                    $set('columns', $cols);
                                }
                            }),

                        Repeater::make('columns')
                            ->label('Kolone i njihov sadržaj')
                            ->helperText('Svaka kolona sadrži svoje blokove koji se na telefonima vertikalno ređaju jedan ispod drugog.')
                            ->schema([
                                Builder::make('blocks')
                                    ->label('Blokovi u koloni')
                                    ->blocks(self::getColumnBlocks())
                                    ->cloneable()
                                    ->collapsed()
                                    ->blockIcons()
                                    ->blockPickerColumns(['default' => 1, 'sm' => 2, 'lg' => 3]),
                            ])
                            ->default([
                                ['blocks' => []],
                                ['blocks' => []],
                            ])
                            ->minItems(1)
                            ->maxItems(4)
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state, $component) => 'Kolona '.((int) preg_replace('/\D/', '', $component->getStatePath()) + 1)),
                    ]),

                Tabs\Tab::make('Stil & Pozadina')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('style.background')
                                ->label('Pozadina sekcije (Background)')
                                ->options([
                                    'default' => 'Podrazumevano (Transparent / Surface)',
                                    'surface_low' => 'Svetla kartica / Surface Low',
                                    'surface_lowest' => 'Bela / Najsvetlija (Lowest)',
                                    'primary_tint' => 'Blagi akcent (Primary Tint 4%)',
                                    'dark' => 'Tamna pozadina (Dark Contrast)',
                                    'brand' => 'Puna brend boja (Solid Brand)',
                                ])
                                ->default('default'),

                            Select::make('style.padding_y')
                                ->label('Vertikalni razmak (Padding Y)')
                                ->options([
                                    'none' => 'Bez razmaka (py-0)',
                                    'sm' => 'Mali razmak (py-6 do py-8)',
                                    'md' => 'Standardni razmak (py-10 do py-16)',
                                    'lg' => 'Veliki razmak (py-16 do py-24)',
                                    'xl' => 'Ekstra veliki (py-20 do py-32)',
                                ])
                                ->default('md'),

                            Select::make('style.max_width')
                                ->label('Maksimalna širina (Max Width)')
                                ->options([
                                    'narrow' => 'Uži kontejner (max-w-4xl)',
                                    'default' => 'Standardni kontejner (max-w-7xl)',
                                    'full' => 'Puna širina ekrana (Full Width)',
                                ])
                                ->default('default'),
                        ]),

                        Grid::make(2)->schema([
                            Select::make('style.gap')
                                ->label('Razmak između kolona (Gap)')
                                ->options([
                                    'none' => 'Bez razmaka (gap-0)',
                                    'sm' => 'Mali razmak (gap-4 do gap-6)',
                                    'md' => 'Standardni razmak (gap-6 do gap-8)',
                                    'lg' => 'Veliki razmak (gap-8 do gap-12)',
                                ])
                                ->default('md'),

                            Select::make('style.items_align')
                                ->label('Vertikalno poravnanje kolona')
                                ->options([
                                    'top' => 'Vrh (Top / Start)',
                                    'center' => 'Sredina (Center)',
                                    'bottom' => 'Dno (Bottom / End)',
                                    'stretch' => 'Istezanje pune visine (Stretch)',
                                ])
                                ->default('top'),
                        ]),
                    ]),

                Tabs\Tab::make('Zaglavlje Sekcije')
                    ->icon('heroicon-o-bars-3-bottom-left')
                    ->schema([
                        Tabs::make('header_translations')
                            ->tabs(
                                TranslatableTabs::make(fn ($lang) => [
                                    TextInput::make("header.badge.{$lang}")
                                        ->label('Oznaka / Badge ('.strtoupper($lang).')')
                                        ->placeholder('npr. NAŠE USLUGE'),

                                    TextInput::make("header.title.{$lang}")
                                        ->label('Glavni naslov sekcije ('.strtoupper($lang).')')
                                        ->placeholder('npr. Sveobuhvatna rešenja za vaš biznis'),

                                    TextInput::make("header.subtitle.{$lang}")
                                        ->label('Podnaslov / Opis ('.strtoupper($lang).')')
                                        ->placeholder('npr. Kratak opis ili uvod u sekciju...'),
                                ])
                            ),

                        Select::make('header.align')
                            ->label('Poravnanje zaglavlja')
                            ->options([
                                'left' => 'Levo (Left)',
                                'center' => 'Centar (Center)',
                            ])
                            ->default('left'),
                    ]),
            ]);

        return $components;
    }
}
