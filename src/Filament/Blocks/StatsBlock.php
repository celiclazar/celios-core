<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Celios\Core\Filament\Helpers\TranslatableTabs;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;

class StatsBlock
{
    public static function make(): Block
    {
        return Block::make('stats_block')
            ->label(fn (?array $state) => BlockLabelHelper::withCount(__('blocks.stats_title'), $state, 'stats', 'statistika', 'statistika'))
            ->icon('heroicon-o-chart-bar')
            ->schema([
                Repeater::make('stats')
                    ->label(fn () => __('blocks.stats_items'))
                    ->schema([
                        TextInput::make('number')
                            ->label(fn () => __('blocks.stats_number'))
                            ->required(),

                        TextInput::make('badge')
                            ->label(fn () => 'Bedž oznaka (npr. 99.8th perc, Green, Novo)'),

                        Tabs::make(fn () => __('blocks.translations'))
                            ->tabs(
                                TranslatableTabs::make(fn ($lang) => [
                                    TextInput::make("label.{$lang}")
                                        ->label(fn () => __('blocks.stats_label') . ' (' . strtoupper($lang) . ')'),

                                    TextInput::make("note.{$lang}")
                                        ->label(fn () => 'Napomena / Podopis (' . strtoupper($lang) . ')'),
                                ])
                            ),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->defaultItems(4),
            ]);
    }
}

