<?php

namespace Celios\Core\Filament\Resources\Categories\Tables;

use Celios\Core\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('fields.title'))
                    ->state(fn (Category $record): string => $record->getTranslation('title', app()->getLocale(), true) ?: __('fields.no_title'))
                    ->searchable(),

                TextColumn::make('slug')
                    ->label(__('fields.url_slug'))
                    ->state(fn (Category $record): string => $record->getTranslation('slug', app()->getLocale(), true) ?: '')
                    ->searchable(),

                TextColumn::make('parent.title')
                    ->label(__('fields.parent_item'))
                    ->state(fn (Category $record): string => $record->parent
                        ? ($record->parent->getTranslation('title', app()->getLocale(), true) ?: __('fields.no_title'))
                        : __('fields.root_level')
                    )
                    ->badge()
                    ->color(fn (Category $record): string => $record->parent ? 'info' : 'gray'),

                TextColumn::make('posts_count')
                    ->label(__('blog.posts_count'))
                    ->counts('posts')
                    ->badge(),

                IconColumn::make('is_visible')
                    ->label(__('fields.visible_on_site'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                \Filament\Actions\Action::make('view_on_site')
                    ->label(__('actions.view'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->openUrlInNewTab()
                    ->url(fn (Category $record): string => $record->getUrl()),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
