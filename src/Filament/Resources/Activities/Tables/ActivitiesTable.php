<?php

namespace Celios\Core\Filament\Resources\Activities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->label(fn () => __('fields.activity_action'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created', 'marked_as_read', 'submission_received' => 'success',
                        'updated', 'marked_as_unread' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'created' => __('fields.action_created'),
                        'updated' => __('fields.action_updated'),
                        'deleted' => __('fields.action_deleted'),
                        'marked_as_read' => __('forms.mark_as_read'),
                        'marked_as_unread' => __('forms.mark_as_unread'),
                        'submission_received' => __('forms.submission_single') . ' - ' . __('forms.submitted_at'),
                        default => Str::headline($state),
                    })
                    ->sortable(),

                TextColumn::make('subject_type')
                    ->label(fn () => __('fields.activity_module'))
                    ->formatStateUsing(fn ($state) => Str::afterLast($state, '\\'))
                    ->searchable(),

                TextColumn::make('causer.name')
                    ->label(fn () => __('fields.activity_performed_by'))
                    ->default(fn () => __('fields.system'))
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label(fn () => __('fields.activity_timestamp'))
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label(fn () => __('actions.view')),
                EditAction::make()->label(fn () => __('actions.edit')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label(fn () => __('actions.delete_selected')),
                ])->label(fn () => __('actions.bulk_actions')),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
