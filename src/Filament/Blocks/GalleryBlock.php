<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class GalleryBlock
{
    public static function make(): Block
    {
        return Block::make('gallery_block')
            ->label(fn (?array $state) => BlockLabelHelper::withCount(__('blocks.gallery_title'), $state, 'images', 'slika', 'slika'))
            ->icon('heroicon-o-rectangle-stack')
            ->schema([
                CuratorPicker::make('images')
                    ->label(fn () => __('blocks.gallery_images'))
                    ->multiple()
                    ->required(),

                Select::make('layout')
                    ->label(fn () => __('blocks.gallery_layout'))
                    ->options(fn (): array => [
                        'grid' => __('blocks.layout_grid'),
                        'slider' => __('blocks.layout_slider'),
                    ])
                    ->default('grid'),

                Select::make('columns')
                    ->label('Broj kolona (za Grid)')
                    ->options([
                        '2' => '2 Kolone',
                        '3' => '3 Kolone',
                        '4' => '4 Kolone',
                    ])
                    ->default('3'),

                Toggle::make('enable_lightbox')
                    ->label('Omogući uvećanje (Lightbox popup)')
                    ->default(true),
            ]);
    }
}

