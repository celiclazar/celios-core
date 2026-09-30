<?php

namespace Celios\Core\Filament\Resources\Users\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(fn () => __('fields.name'))
                    ->searchable(),
                TextColumn::make('username')
                    ->label(fn () => __('fields.username'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(fn () => __('fields.email'))
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->label(fn () => __('fields.email_verified_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label(fn () => __('fields.phone'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(fn () => __('fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('fields.status_active') : __('fields.status_inactive'))
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->icon(fn (bool $state): string => $state ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                    ->searchable(),
                CuratorColumn::make('avatar')
                    ->label(fn () => __('fields.avatar'))
                    ->imageSize(40)
                    ->circular(),
                TextColumn::make('last_login_at')
                    ->label(fn () => __('fields.last_login_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_login_ip')
                    ->label(fn () => __('fields.last_login_ip'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(fn () => __('fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(fn () => __('fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(fn () => __('fields.deleted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->label(fn () => __('actions.edit')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label(fn () => __('actions.delete_selected')),
                ])->label(fn () => __('actions.bulk_actions')),
            ]);
    }
}
