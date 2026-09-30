<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Celios\Core\Filament\Helpers\TranslatableTabs;
use Celios\Core\Support\Translations;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;

class HeroBlock
{
    public static function make(): Block
    {
        return Block::make('hero_section')
            ->label(fn (?array $state) => BlockLabelHelper::make(__('blocks.hero_title'), $state, ['heading', 'badge_text', 'subtitle']))
            ->icon('heroicon-o-sparkles')
            ->schema([
                Tabs::make(fn () => __('blocks.translations'))
                    ->tabs(
                        TranslatableTabs::make(fn ($lang) => [
                            TextInput::make("badge_text.{$lang}")
                                ->label(fn () => 'Bedž oznaka (' . strtoupper($lang) . ')'),

                            TextInput::make("heading.{$lang}")
                                ->label(Translations::get($lang, 'heading'))
                                ->required($lang === 'sr'),

                            TextInput::make("highlighted_text.{$lang}")
                                ->label(fn () => 'Akcentovani / Podvučeni tekst (' . strtoupper($lang) . ')'),

                            Textarea::make("subtitle.{$lang}")
                                ->label(Translations::get($lang, 'subtitle'))
                                ->rows(3),

                            TextInput::make("button_text.{$lang}")
                                ->label(fn () => __('blocks.button_text') . ' (' . strtoupper($lang) . ')'),
                        ])
                    ),

                TextInput::make('button_url')
                    ->label(fn () => __('blocks.button_url')),

                CuratorPicker::make('background_image_id')
                    ->label(fn () => __('blocks.background_image')),
            ]);
    }
}

