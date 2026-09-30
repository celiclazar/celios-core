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

class FAQBlock
{
    public static function make(): Block
    {
        return Block::make('faq_block')
            ->label(fn (?array $state) => BlockLabelHelper::withCount(__('blocks.faq_title'), $state, 'items', 'pitanje', 'pitanja'))
            ->icon('heroicon-o-question-mark-circle')
            ->schema([
                Tabs::make(fn () => __('blocks.translations'))
                    ->tabs(
                        TranslatableTabs::make(fn ($lang) => [
                            TextInput::make("section_tag.{$lang}")
                                ->label(fn () => 'Sekcijska oznaka (' . strtoupper($lang) . ')'),

                            TextInput::make("heading.{$lang}")
                                ->label(fn () => 'Glavni naslov (' . strtoupper($lang) . ')'),

                            Textarea::make("subtitle.{$lang}")
                                ->label(fn () => 'Podnaslov (' . strtoupper($lang) . ')')
                                ->rows(2),
                        ])
                    ),

                Repeater::make('items')
                    ->label(fn () => __('blocks.faq_items'))
                    ->schema([
                        Tabs::make(fn () => __('blocks.translations'))
                            ->tabs(
                                TranslatableTabs::make(fn ($lang) => [
                                    TextInput::make("question.{$lang}")
                                        ->label(Translations::get($lang, 'question'))
                                        ->required($lang === 'sr'),

                                    Textarea::make("answer.{$lang}")
                                        ->label(Translations::get($lang, 'answer'))
                                        ->rows(3)
                                        ->required($lang === 'sr'),
                                ])
                            ),
                    ])
                    ->collapsible()
                    ->defaultItems(3),
            ]);
    }
}

