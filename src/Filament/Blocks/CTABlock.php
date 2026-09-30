<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Celios\Core\Filament\Helpers\TranslatableTabs;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;

class CTABlock
{
    public static function make(): Block
    {
        return Block::make('cta_block')
            ->label(fn (?array $state) => BlockLabelHelper::make(__('blocks.cta_title'), $state, ['heading', 'text', 'status_pill']))
            ->icon('heroicon-o-megaphone')
            ->schema([
                Tabs::make(fn () => __('blocks.translations'))
                    ->tabs(
                        TranslatableTabs::make(fn ($lang) => [
                            TextInput::make("status_pill.{$lang}")
                                ->label(fn () => 'Status oznaka / Pill (' . strtoupper($lang) . ')'),

                            TextInput::make("heading.{$lang}")
                                ->label(fn () => __('blocks.heading') . ' (' . strtoupper($lang) . ')'),

                            TextInput::make("text.{$lang}")
                                ->label(fn () => __('blocks.text') . ' (' . strtoupper($lang) . ')'),

                            TextInput::make("button_text.{$lang}")
                                ->label(fn () => __('blocks.button_text') . ' (' . strtoupper($lang) . ')'),

                            TextInput::make("footer_note_1.{$lang}")
                                ->label(fn () => 'Napomena 1 (' . strtoupper($lang) . ')'),

                            TextInput::make("footer_note_2.{$lang}")
                                ->label(fn () => 'Napomena 2 (' . strtoupper($lang) . ')'),
                        ])
                    ),

                TextInput::make('button_url')
                    ->label(fn () => __('blocks.button_url')),
            ]);
    }
}

