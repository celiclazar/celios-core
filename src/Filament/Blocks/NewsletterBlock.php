<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Celios\Core\Filament\Helpers\TranslatableTabs;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;

class NewsletterBlock
{
    public static function make(): Block
    {
        return Block::make('newsletter')
            ->label(fn (?array $state) => BlockLabelHelper::make(__('newsletter.block_title'), $state, ['title', 'description']))
            ->icon('heroicon-o-envelope')
            ->schema([
                Tabs::make(fn () => __('blocks.translations'))
                    ->tabs(
                        TranslatableTabs::make(fn ($lang) => [
                            TextInput::make("title.{$lang}")
                                ->label(fn () => __('newsletter.block_heading') . ' (' . strtoupper($lang) . ')')
                                ->placeholder(fn () => __('newsletter.block_default_title', [], $lang))
                                ->default(fn () => __('newsletter.block_default_title', [], $lang)),

                            Textarea::make("description.{$lang}")
                                ->label(fn () => __('newsletter.block_description') . ' (' . strtoupper($lang) . ')')
                                ->rows(3)
                                ->placeholder(fn () => __('newsletter.block_default_description', [], $lang))
                                ->default(fn () => __('newsletter.block_default_description', [], $lang)),

                            TextInput::make("button_text.{$lang}")
                                ->label(fn () => __('newsletter.block_button_text') . ' (' . strtoupper($lang) . ')')
                                ->placeholder(fn () => __('newsletter.block_default_button', [], $lang))
                                ->default(fn () => __('newsletter.block_default_button', [], $lang)),

                            TextInput::make("disclaimer.{$lang}")
                                ->label(fn () => __('newsletter.block_disclaimer') . ' (' . strtoupper($lang) . ')')
                                ->placeholder(fn () => __('newsletter.block_default_disclaimer', [], $lang))
                                ->default(fn () => __('newsletter.block_default_disclaimer', [], $lang)),
                        ])
                    ),
            ]);
    }
}
