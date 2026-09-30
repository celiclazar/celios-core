<?php

namespace Celios\Core\Filament\Widgets\Queue;

use Celios\Core\Services\Queue\QueueInspectorManager;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection;

class PendingJobsTableWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public string $selectedQueue = 'default';

    public function getHeading(): string
    {
        return 'Pending & In-Flight Jobs';
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(function (): Collection {
                return app(QueueInspectorManager::class)->getPendingJobs($this->selectedQueue, 100);
            })
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),

                TextColumn::make('job_name')
                    ->label('Job Class')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('queue')
                    ->label('Queue')
                    ->badge()
                    ->color('info'),

                TextColumn::make('attempts')
                    ->label('Attempts')
                    ->alignCenter(),

                IconColumn::make('is_reserved')
                    ->label('In Flight')
                    ->boolean()
                    ->trueIcon('heroicon-m-arrow-path')
                    ->falseIcon('heroicon-m-clock')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                TextColumn::make('available_at')
                    ->label('Available At')
                    ->placeholder('Immediately'),

                TextColumn::make('created_at')
                    ->label('Queued At')
                    ->placeholder('-'),
            ])
            ->recordActions([
                Action::make('viewPayload')
                    ->label('Payload')
                    ->icon('heroicon-o-code-bracket')
                    ->modalHeading(fn (array $record): string => 'Payload: '.($record['job_name'] ?? 'Job'))
                    ->modalContent(fn (array $record) => view('filament.pages.queue.payload-modal', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Pending Job')
                    ->modalDescription('Are you sure you want to remove this job from the queue?')
                    ->action(function (array $record) {
                        $deleted = app(QueueInspectorManager::class)->deletePendingJob(
                            $record['id'],
                            $record['queue'] ?? 'default'
                        );

                        if ($deleted) {
                            Notification::make()
                                ->title('Job removed from queue')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Failed to remove job')
                                ->body('Job might have already been processed or reserved by a worker.')
                                ->warning()
                                ->send();
                        }
                    }),
            ])
            ->paginated(false);
    }
}
