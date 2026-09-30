<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Celios\Core\Filament\Helpers\TranslatableTabs;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;

class FeaturesCardsBlock
{
    public static function make(): Block
    {
        return Block::make('features_cards')
            ->label(fn (?array $state) => BlockLabelHelper::withCount(__('blocks.features_cards_title') ?? 'Features Cards', $state, 'cards', 'kartica', 'kartica'))
            ->icon('heroicon-o-squares-plus')
            ->schema([
                Tabs::make(fn () => __('blocks.translations'))
                    ->tabs(
                        TranslatableTabs::make(fn ($lang) => [
                            TextInput::make("section_tag.{$lang}")
                                ->label('Tag sekcije (' . strtoupper($lang) . ')')
                                ->default('Arhitektura & Mogućnosti'),

                            TextInput::make("section_title.{$lang}")
                                ->label('Naslov sekcije (' . strtoupper($lang) . ')')
                                ->required($lang === 'sr')
                                ->default('Zašto programeri i dizajneri biraju Celios'),

                            Textarea::make("section_desc.{$lang}")
                                ->label('Opis sekcije (' . strtoupper($lang) . ')')
                                ->rows(2)
                                ->default('Bez komplikovanih eksternih servisa ili glomaznih dodataka. Sve je napisano u modernom Laravelu prateći najviše inženjerske i vizuelne standarde.'),
                        ])
                    ),

                Repeater::make('cards')
                    ->label('Kartice mogućnosti')
                    ->schema([
                        TextInput::make('icon')
                            ->label('Material Symbols ikonica (npr. translate, speed, widgets)')
                            ->default('widgets')
                            ->required(),

                        Tabs::make(fn () => __('blocks.translations'))
                            ->tabs(
                                TranslatableTabs::make(fn ($lang) => [
                                    TextInput::make("title.{$lang}")
                                        ->label('Naslov kartice (' . strtoupper($lang) . ')')
                                        ->required($lang === 'sr'),

                                    Textarea::make("description.{$lang}")
                                        ->label('Opis kartice (' . strtoupper($lang) . ')')
                                        ->rows(3),

                                    Textarea::make("checklist.{$lang}")
                                        ->label('Stavke sa kvačicom (jedna po liniji) (' . strtoupper($lang) . ')')
                                        ->rows(2)
                                        ->helperText('Unesite stavke razdvojene novim redom'),
                                ])
                            ),
                    ])
                    ->collapsible()
                    ->defaultItems(3),
            ]);
    }
}
