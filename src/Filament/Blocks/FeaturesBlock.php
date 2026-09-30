<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Celios\Core\Filament\Helpers\TranslatableTabs;
use Celios\Core\Support\Translations;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;

class FeaturesBlock
{
    public static function make(): Block
    {
        return Block::make('features_block')
            ->label(fn (?array $state) => BlockLabelHelper::withCount(__('blocks.features_title'), $state, 'features', 'stavka', 'stavki'))
            ->icon('heroicon-o-squares-2x2')
            ->schema([
                Tabs::make(fn () => __('blocks.translations'))
                    ->tabs(
                        TranslatableTabs::make(fn ($lang) => [
                            TextInput::make("section_tag.{$lang}")
                                ->label(fn () => 'Sekcijska oznaka / Nadnaslov (' . strtoupper($lang) . ')'),

                            TextInput::make("section_title.{$lang}")
                                ->label(fn () => 'Glavni naslov sekcije (' . strtoupper($lang) . ')'),

                            Textarea::make("section_desc.{$lang}")
                                ->label(fn () => 'Opis sekcije (' . strtoupper($lang) . ')')
                                ->rows(2),
                        ])
                    ),

                Repeater::make('features')
                    ->label(fn () => __('blocks.features_items'))
                    ->schema([
                        TextInput::make('icon')
                            ->label(fn () => __('blocks.icon') . ' (Material Symbol / Heroicon npr. translate, speed, widgets, star)')
                            ->default('widgets'),

                        Tabs::make(fn () => __('blocks.translations'))
                            ->tabs(
                                TranslatableTabs::make(fn ($lang) => [
                                    TextInput::make("title.{$lang}")
                                        ->label(Translations::get($lang, 'title')),

                                    Textarea::make("description.{$lang}")
                                        ->label(Translations::get($lang, 'description'))
                                        ->rows(3),

                                    Textarea::make("checklist.{$lang}")
                                        ->label(fn () => 'Lista stavki / Čeklista (svaka stavka u novom redu)')
                                        ->rows(2),
                                ])
                            ),
                    ])
                    ->collapsible()
                    ->defaultItems(3),
            ]);
    }
}
