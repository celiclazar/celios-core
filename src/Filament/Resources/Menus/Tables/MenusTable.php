<?php

namespace Celios\Core\Filament\Resources\Menus\Tables;

use Celios\Core\Filament\Resources\Menus\MenuResource;
use Celios\Core\Models\Menu;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('fields.name') ?? 'Menu Name')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('locations')
                    ->label(__('menus.locations') ?? 'Display Locations')
                    ->state(function (Menu $record): array {
                        $assigned = $record->getAssignedLocations();
                        $themeLocations = app(\Celios\Core\Services\ThemeManager::class)->getMenuLocations();

                        return array_values(array_map(fn ($key) => $themeLocations[$key] ?? ucfirst(str_replace('_', ' ', $key)), $assigned));
                    })
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),
                TextColumn::make('menu_items_count')
                    ->label(__('menus.items_count') ?? 'Links Count')
                    ->counts('menuItems')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('key')
                    ->label(__('fields.key'))
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                DeleteAction::make()
                    ->label(__('actions.delete'))
                    ->hidden(fn (Menu $record): bool => $record->isSystem()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('actions.delete_selected'))
                        ->action(function (Collection $records) {
                            $records->reject(fn (Menu $menu) => $menu->isSystem())->each->delete();
                        }),
                ])->label(__('actions.bulk_actions')),
            ]);
    }
}
