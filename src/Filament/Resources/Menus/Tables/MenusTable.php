<?php

namespace Celios\Core\Filament\Resources\Menus\Tables;

use Celios\Core\Filament\Resources\Menus\MenuResource;
use Celios\Core\Models\Menu;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label(__('fields.key'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('fields.value'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('manage')
                    ->label(__('actions.manage_links'))
                    ->icon('heroicon-o-rectangle-group')
                    ->color('success')
                    ->url(fn (Menu $record): string => MenuResource::getUrl('manage', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
