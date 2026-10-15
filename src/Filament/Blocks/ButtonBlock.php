<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Celios\Core\Filament\Helpers\TranslatableTabs;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;

class ButtonBlock
{
    public static function make(): Block
    {
        return Block::make('button_block')
            ->label(fn (?array $state) => BlockLabelHelper::make(__('blocks.button_block_title'), $state, ['label', 'url']))
            ->icon('heroicon-o-cursor-arrow-rays')
            ->schema([
                Tabs::make(fn () => __('blocks.translations'))
                    ->tabs(
                        TranslatableTabs::make(fn ($lang) => [
                            TextInput::make("label.{$lang}")
                                ->label(fn () => __('blocks.button_text').' ('.strtoupper($lang).')')
                                ->placeholder('Saznajte više... / Learn more...'),
                        ])
                    ),

                Grid::make(3)
                    ->schema([
                        TextInput::make('url')
                            ->label(fn () => __('blocks.button_url'))
                            ->placeholder('https://... ili /kontakt')
                            ->default('#'),

                        Select::make('variant')
                            ->label('Stil dugmeta / Variant')
                            ->options([
                                'primary' => 'Primarno (Brand)',
                                'secondary' => 'Sekundarno (Surface)',
                                'outline' => 'Outline (Oivičeno)',
                                'dark' => 'Tamno (Contrast)',
                                'accent' => 'Akcent (Gradient)',
                            ])
                            ->default('primary'),

                        Select::make('size')
                            ->label('Veličina / Size')
                            ->options([
                                'sm' => 'Malo (Small)',
                                'md' => 'Standardno (Medium)',
                                'lg' => 'Veliko (Large)',
                            ])
                            ->default('md'),
                    ]),

                Grid::make(3)
                    ->schema([
                        Select::make('align')
                            ->label('Poravnanje / Alignment')
                            ->options([
                                'left' => 'Levo (Left)',
                                'center' => 'Centar (Center)',
                                'right' => 'Desno (Right)',
                                'stretch' => 'Puna širina (Full Width)',
                            ])
                            ->default('left'),

                        Select::make('icon')
                            ->label('Ikonica / Icon')
                            ->options([
                                'none' => 'Bez ikonice',
                                'arrow-right' => 'Strelica desno (→)',
                                'external-link' => 'Spoljni link (↗)',
                                'sparkles' => 'Zvezdice (✨)',
                                'download' => 'Preuzimanje (↓)',
                                'check' => 'Kvačica (✓)',
                            ])
                            ->default('arrow-right'),

                        Toggle::make('open_in_new_tab')
                            ->label('Otvori u novom tabu (_blank)')
                            ->default(false),
                    ]),
            ]);
    }
}
