<?php

namespace Celios\Core\Filament\Resources\DocumentCategories\Tables;

use Celios\Core\Models\DocumentCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('fields.title'))
                    ->state(fn (DocumentCategory $record): string => $record->getLocalizedTitle())
                    ->searchable(),

                TextColumn::make('slug')
                    ->label(__('fields.url_slug'))
                    ->state(fn (DocumentCategory $record): string => $record->getLocalizedSlug())
                    ->searchable(),

                TextColumn::make('parent.title')
                    ->label(__('fields.parent_item'))
                    ->state(fn (DocumentCategory $record): string => $record->parent
                        ? $record->parent->getLocalizedTitle()
                        : __('fields.root_level')
                    )
                    ->badge()
                    ->color(fn (DocumentCategory $record): string => $record->parent ? 'info' : 'gray'),

                TextColumn::make('documents_count')
                    ->label(__('sidebar.documents'))
                    ->counts('documents')
                    ->badge(),

                IconColumn::make('is_internal')
                    ->label(__('documents.is_internal'))
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('danger')
                    ->falseColor('gray'),

                IconColumn::make('is_active')
                    ->label(__('fields.active'))
                    ->boolean(),

                TextColumn::make('order')
                    ->label(__('fields.order'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order', 'asc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
