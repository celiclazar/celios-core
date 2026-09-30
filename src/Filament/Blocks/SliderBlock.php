<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;

class SliderBlock
{
    public static function make(): Block
    {
        return Block::make('slider_block')
            ->label(fn (?array $state) => BlockLabelHelper::withCount(__('blocks.slider_title'), $state, 'images', 'slajd', 'slajdova'))
            ->icon('heroicon-o-arrow-long-right')
            ->schema([
                Toggle::make('autoplay')
                    ->label(fn () => __('blocks.sliders_autoplay'))
                    ->default(true),

                FileUpload::make('images')
                    ->label(fn () => __('blocks.slider_images'))
                    ->multiple()
                    ->reorderable()
                    ->image()
                    ->directory('cms/sliders')
                    ->required(),

                TextInput::make('button_url')
                    ->label(fn () => __('blocks.button_url'))
                    ->placeholder('https://... ili /sr/kontakt'),

                Tabs::make(fn () => __('blocks.translations'))
                    ->tabs(
                        collect(config('locales.available', ['sr', 'en']))->map(function ($lang) {
                            return Tabs\Tab::make(strtoupper($lang))
                                ->schema([
                                    TextInput::make("title.{$lang}")
                                        ->label(fn () => __('blocks.sliders_title')),

                                    TextInput::make("subtitle.{$lang}")
                                        ->label(fn () => __('blocks.sliders_subtitle')),

                                    TextInput::make("button_text.{$lang}")
                                        ->label(fn () => __('blocks.button_text')),
                                ]);
                        })->toArray()
                    ),
            ]);
    }
}
