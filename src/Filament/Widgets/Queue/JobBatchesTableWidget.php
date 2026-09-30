<?php

namespace Celios\Core\Filament\Widgets\Queue;

use Celios\Core\Models\JobBatch;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Bus;

class JobBatchesTableWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return 'Job Batches';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => JobBatch::query()->latest('created_at'))
            ->columns([
                TextColumn::make('id')
                    ->label('Batch ID')
                    ->limit(10)
                    ->copyable()
                    ->tooltip(fn (JobBatch $record): string => $record->id),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('progress_percentage')
                    ->label('Progress')
                    ->formatStateUsing(fn (int $state): string => "{$state}%")
                    ->badge()
                    ->color(fn (int $state): string => $state >= 100 ? 'success' : 'primary'),

                TextColumn::make('total_jobs')
                    ->label('Total'),

                TextColumn::make('pending_jobs')
                    ->label('Pending'),

                TextColumn::make('failed_jobs')
                    ->label('Failed')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'finished' => 'success',
                        'finished_with_failures' => 'danger',
                        'cancelled' => 'warning',
                        default => 'info',
                    }),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->visible(fn (JobBatch $record): bool => is_null($record->cancelled_at) && is_null($record->finished_at))
                    ->requiresConfirmation()
                    ->action(function (JobBatch $record) {
                        $batch = Bus::findBatch($record->id);
                        if ($batch) {
                            $batch->cancel();
                        } else {
                            $record->update(['cancelled_at' => now()]);
                        }

                        Notification::make()
                            ->title('Batch cancelled')
                            ->warning()
                            ->send();
                    }),
            ])
            ->defaultPaginationPageOption(10);
    }
}
