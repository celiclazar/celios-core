<?php

namespace Celios\Core\Filament\Resources\Activities\Tables;

use Celios\Core\Models\User;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

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
                        'submission_received' => __('forms.submission_single').' - '.__('forms.submitted_at'),
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
                SelectFilter::make('subject_type')
                    ->label(fn () => __('fields.activity_module'))
                    ->options(fn () => Activity::query()
                        ->whereNotNull('subject_type')
                        ->distinct()
                        ->pluck('subject_type')
                        ->mapWithKeys(fn (string $type) => [$type => Str::headline(class_basename($type))])
                        ->sort()
                        ->toArray()
                    )
                    ->searchable(),

                SelectFilter::make('causer')
                    ->label(fn () => __('fields.activity_performed_by'))
                    ->options(function () {
                        $users = User::query()
                            ->whereIn('id', Activity::query()->whereNotNull('causer_id')->select('causer_id'))
                            ->pluck('name', 'id')
                            ->toArray();

                        return [
                            'system' => __('fields.system'),
                            ...$users,
                        ];
                    })
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if (blank($value)) {
                            return $query;
                        }

                        if ($value === 'system') {
                            return $query->whereNull('causer_id');
                        }

                        return $query->where('causer_id', $value);
                    })
                    ->searchable(),

                SelectFilter::make('action')
                    ->label(fn () => __('fields.activity_action'))
                    ->options([
                        'created' => __('fields.action_created'),
                        'updated' => __('fields.action_updated'),
                        'deleted' => __('fields.action_deleted'),
                        'marked_as_read' => __('forms.mark_as_read'),
                        'marked_as_unread' => __('forms.mark_as_unread'),
                        'submission_received' => __('forms.submission_single').' - '.__('forms.submitted_at'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if (blank($value)) {
                            return $query;
                        }

                        return $query->where(function (Builder $q) use ($value) {
                            $q->where('description', $value)
                                ->orWhere('event', $value)
                                ->orWhere('description', 'like', "% {$value}");
                        });
                    }),
            ])
            ->recordActions([
                ViewAction::make()->label(fn () => __('actions.view')),
            ])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }
}
