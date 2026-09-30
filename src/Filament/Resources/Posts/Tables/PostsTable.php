<?php

namespace Celios\Core\Filament\Resources\Posts\Tables;

use Celios\Core\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('fields.title'))
                    ->state(fn (Post $record): string => $record->getTranslation('title', app()->getLocale(), true) ?: __('fields.no_title'))
                    ->searchable(),

                TextColumn::make('category.title')
                    ->label(__('blog.category'))
                    ->state(fn (Post $record): string => $record->category
                        ? ($record->category->getTranslation('title', app()->getLocale(), true) ?: __('fields.no_title'))
                        : '-'
                    )
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('author.name')
                    ->label(__('blog.author'))
                    ->toggleable(),

                IconColumn::make('is_published')
                    ->label(__('blog.is_published'))
                    ->boolean(),

                TextColumn::make('published_at')
                    ->label(__('blog.published_at'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('views_count')
                    ->label(__('blog.views'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
            ->recordActions([
                EditAction::make(),
                Action::make('view_on_site')
                    ->label(__('actions.view'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->openUrlInNewTab()
                    ->url(fn (Post $record): string => $record->getUrl()),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
