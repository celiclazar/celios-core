<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Tabs;

class TextBlock
{
    public static function make(): Block
    {
        return Block::make('text_block')
            ->label(fn (?array $state) => BlockLabelHelper::make(__('blocks.text_block_title'), $state, ['body']))
            ->icon('heroicon-o-document-text')
            ->schema([
                Tabs::make(fn () => __('blocks.translations'))
                    ->tabs(
                        collect(['sr', 'en', 'it'])->map(function ($lang) {
                            return Tabs\Tab::make(strtoupper($lang))
                                ->schema([
                                    RichEditor::make("body.{$lang}")
                                        ->label(fn () => __('blocks.content'))
                                        ->toolbarButtons([
                                            'bold',
                                            'italic',
                                            'link',
                                            'bulletList',
                                            'orderedList',
                                        ]),
                                ]);
                        })->toArray()
                    ),
            ]);
    }
}
