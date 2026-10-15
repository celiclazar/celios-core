<?php

namespace Celios\Core\Filament\Resources\Sections\Tables;

use Celios\Core\Models\Section;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Naziv sekcije')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('layout')
                    ->label('Raspored')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '1_col' => '1 Kolona',
                        '2_cols' => '2 Kolone (50/50)',
                        '3_cols' => '3 Kolone (33/33/33)',
                        '4_cols' => '4 Kolone',
                        '1_2_cols' => '1/3 + 2/3',
                        '2_1_cols' => '2/3 + 1/3',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '1_col' => 'gray',
                        '2_cols' => 'primary',
                        '3_cols' => 'success',
                        '4_cols' => 'warning',
                        default => 'info',
                    }),

                TextColumn::make('category')
                    ->label('Kategorija')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('columns_count')
                    ->label('Broj kolona')
                    ->state(fn (Section $record): int => is_array($record->columns) ? count($record->columns) : 0)
                    ->badge(),

                IconColumn::make('is_active')
                    ->label('Aktivno')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Izmenjeno')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                ReplicateAction::make()
                    ->excludeAttributes(['slug'])
                    ->beforeReplicaSaved(function (Section $replica) {
                        $replica->name = $replica->name.' (Kopija)';
                        $replica->slug = null;
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
