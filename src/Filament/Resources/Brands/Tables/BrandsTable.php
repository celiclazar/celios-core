<?php

namespace Celios\Core\Filament\Resources\Brands\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Celios\Core\Models\Brand;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BrandsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('logo_light_id')
                    ->label(fn () => __('brands.logo_light'))
                    ->imageSize(36)
                    ->circular(false),

                TextColumn::make('name')
                    ->label(fn () => __('brands.name'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (Brand $record): ?string => $record->headline),

                TextColumn::make('primary_domain')
                    ->label(fn () => __('brands.primary_domain'))
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('default_locale')
                    ->label(fn () => __('brands.default_locale'))
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

                TextColumn::make('default_currency')
                    ->label(fn () => __('brands.default_currency'))
                    ->badge()
                    ->color('success'),

                IconColumn::make('is_default')
                    ->label(fn () => __('brands.is_default'))
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(fn () => __('brands.is_active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label(fn () => __('fields.updated_at') ?? 'Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (Brand $record, DeleteAction $action) {
                        if ($record->is_default) {
                            Notification::make()
                                ->title(__('brands.cannot_delete_default_brand'))
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    }),
            ]);
    }
}
