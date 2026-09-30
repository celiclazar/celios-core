<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;

class ImageBlock
{
    public static function make(): Block
    {
        return Block::make('image_block')
            ->label(fn (?array $state) => BlockLabelHelper::make(__('blocks.image_title'), $state, ['caption', 'alt']))
            ->icon('heroicon-o-photo')
            ->schema([
                CuratorPicker::make('image_id')
                    ->label(fn () => __('blocks.image'))
                    ->required(),

                Tabs::make(fn () => __('blocks.translations'))
                    ->tabs(
                        collect(config('locales.available', []))->map(function ($name, $code) {
                            return Tabs\Tab::make(strtoupper($code))
                                ->schema([
                                    TextInput::make("caption.{$code}")
                                        ->label(fn () => (__('blocks.image_caption')) . ' (' . strtoupper($code) . ')'),

                                    TextInput::make("alt.{$code}")
                                        ->label(fn () => (__('blocks.image_alt')) . ' (' . strtoupper($code) . ')'),
                                ]);
                        })->toArray()
                    ),
            ]);
    }
}
