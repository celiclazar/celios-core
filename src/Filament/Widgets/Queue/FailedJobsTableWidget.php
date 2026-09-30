<?php

namespace Celios\Core\Filament\Widgets\Queue;

use Celios\Core\Models\FailedJob;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;

class FailedJobsTableWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return 'Failed Queue Jobs';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => FailedJob::query()->latest('id'))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('job_name')
                    ->label('Job Class')
                    ->weight('bold')
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->where('payload', 'like', "%{$search}%");
                    }),

                TextColumn::make('queue')
                    ->label('Queue')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('exception_summary')
                    ->label('Exception')
                    ->limit(65)
                    ->tooltip(fn (FailedJob $record): string => $record->exception_summary)
                    ->color('danger'),

                TextColumn::make('failed_at')
                    ->label('Failed At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('retryAll')
                    ->label('Retry All')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Retry All Failed Jobs')
                    ->modalDescription('Are you sure you want to push all failed jobs back onto the queue?')
                    ->action(function () {
                        Artisan::call('queue:retry', ['id' => ['all']]);
                        Notification::make()
                            ->title('Retry signal sent')
                            ->body('All failed jobs have been pushed back to the queue.')
                            ->success()
                            ->send();
                    }),

                Action::make('flushAll')
                    ->label('Flush All')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Flush Failed Jobs')
                    ->modalDescription('This will permanently delete all records from the failed jobs table!')
                    ->action(function () {
                        Artisan::call('queue:flush');
                        Notification::make()
                            ->title('Failed jobs deleted')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('viewDetails')
                    ->label('Details')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (FailedJob $record): string => 'Failed Job: '.$record->job_name)
                    ->modalContent(fn (FailedJob $record) => view('filament.pages.queue.exception-modal', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->action(function (FailedJob $record) {
                        Artisan::call('queue:retry', ['id' => [$record->uuid]]);
                        Notification::make()
                            ->title("Job {$record->uuid} queued for retry")
                            ->success()
                            ->send();
                    }),

                Action::make('delete')
                    ->label('Forget')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (FailedJob $record) {
                        Artisan::call('queue:forget', ['id' => $record->uuid]);
                        Notification::make()
                            ->title('Failed job removed')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    Action::make('bulkRetry')
                        ->label('Retry Selected')
                        ->icon('heroicon-o-arrow-path')
                        ->action(function (Collection $records) {
                            $uuids = $records->pluck('uuid')->toArray();
                            Artisan::call('queue:retry', ['id' => $uuids]);
                            Notification::make()
                                ->title(count($uuids).' jobs queued for retry')
                                ->success()
                                ->send();
                        }),

                    Action::make('bulkForget')
                        ->label('Forget Selected')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                Artisan::call('queue:forget', ['id' => $record->uuid]);
                            }
                            Notification::make()
                                ->title(count($records).' failed jobs deleted')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultPaginationPageOption(10);
    }
}
