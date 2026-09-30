<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Celios\Core\Models\Document;
use Celios\Core\Models\DocumentCategory;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;

class DocumentsBlock
{
    public static function make(): Block
    {
        return Block::make('documents_block')
            ->label(fn (?array $state) => BlockLabelHelper::make(__('documents.block_title'), $state, ['heading', 'subtitle']))
            ->icon('heroicon-o-document-duplicate')
            ->schema([
                Tabs::make('translations')
                    ->tabs(
                        collect(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']))
                            ->map(function ($label, $lang) {
                                return Tabs\Tab::make(strtoupper($lang) . " ({$label})")
                                    ->schema([
                                        TextInput::make("heading.{$lang}")
                                            ->label(__('fields.heading') . ' (' . strtoupper($lang) . ')')
                                            ->placeholder(__('documents.documents')),

                                        TextInput::make("subtitle.{$lang}")
                                            ->label(__('fields.subtitle') . ' (' . strtoupper($lang) . ')'),
                                    ]);
                            })
                            ->values()
                            ->all()
                    ),

                Grid::make(2)
                    ->schema([
                        Select::make('selection_mode')
                            ->label(__('documents.block_selection_mode'))
                            ->options([
                                'category' => __('documents.block_mode_category'),
                                'manual' => __('documents.block_mode_manual'),
                            ])
                            ->default('category')
                            ->live(),

                        Select::make('category_id')
                            ->label(__('documents.category'))
                            ->placeholder(__('documents.all_categories'))
                            ->options(function (): array {
                                return DocumentCategory::query()
                                    ->public()
                                    ->get()
                                    ->mapWithKeys(fn (DocumentCategory $cat) => [
                                        $cat->id => $cat->getLocalizedTitle(),
                                    ])
                                    ->all();
                            })
                            ->searchable()
                            ->nullable()
                            ->visible(fn (callable $get) => $get('selection_mode') !== 'manual'),

                        Select::make('document_ids')
                            ->label(__('documents.block_documents_select'))
                            ->options(function (): array {
                                return Document::query()
                                    ->publicOnly()
                                    ->get()
                                    ->mapWithKeys(fn (Document $doc) => [
                                        $doc->id => $doc->getLocalizedTitle(),
                                    ])
                                    ->all();
                            })
                            ->multiple()
                            ->searchable()
                            ->visible(fn (callable $get) => $get('selection_mode') === 'manual')
                            ->columnSpanFull(),

                        Select::make('limit')
                            ->label(__('fields.limit'))
                            ->options([
                                3 => '3',
                                4 => '4',
                                6 => '6',
                                8 => '8',
                                12 => '12',
                            ])
                            ->default(6),

                        Select::make('layout')
                            ->label(__('documents.block_layout'))
                            ->options([
                                'grid' => __('documents.block_layout_cards'),
                                'list' => __('documents.block_layout_list'),
                                'table' => __('documents.block_layout_table'),
                            ])
                            ->default('grid'),

                        Toggle::make('show_size')
                            ->label(__('documents.block_show_size'))
                            ->default(true),

                        Toggle::make('show_version')
                            ->label(__('documents.block_show_version'))
                            ->default(true),

                        Toggle::make('show_date')
                            ->label(__('documents.block_show_date'))
                            ->default(true),

                        Toggle::make('show_downloads')
                            ->label(__('documents.block_show_downloads'))
                            ->default(false),
                    ]),
            ]);
    }
}
