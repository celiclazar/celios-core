<?php

namespace Celios\Core\Filament\Pages;

use Celios\Core\Jobs\Developer\TestQueueJob;
use Celios\Core\Services\Queue\QueueInspectorManager;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use UnitEnum;

class QueueManager extends Page
{
    protected static string|null|BackedEnum $navigationIcon = 'heroicon-o-queue-list';

    protected static ?int $navigationSort = 7;

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_system');
    }

    protected string $view = 'filament.pages.queue-manager';

    public string $activeTab = 'failed';

    public array $stats = [];

    public static function canAccess(): bool
    {
        if (! \Celios\Core\Services\ModuleManager::isEnabled('queue_manager')) {
            return false;
        }

        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasRole('super_admin') || $user->can('page_QueueManager');
    }

    public static function shouldRegisterNavigation(): bool
    {
        if (! \Celios\Core\Services\ModuleManager::isEnabled('queue_manager')) {
            return false;
        }

        return parent::shouldRegisterNavigation();
    }

    public function mount(): void
    {
        $this->refreshStats();
    }

    public function refreshStats(): void
    {
        $this->stats = app(QueueInspectorManager::class)->getStats();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->refreshStats();
    }

    protected function getHeaderActions(): array
    {
        $actions = [
            Action::make('dispatchTestJob')
                ->label('Dispatch Test Job')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->modalHeading('Dispatch Developer Test Job')
                ->modalDescription('Push a synthetic test job into the queue to verify worker execution.')
                ->form([
                    Select::make('behavior')
                        ->label('Job Behavior')
                        ->options([
                            'success' => 'Success (Executes and logs normally)',
                            'fail' => 'Deliberate Failure (Throws exception to test failed_jobs table)',
                            'delayed' => 'Delayed (Delays execution by 15 seconds)',
                        ])
                        ->default('success')
                        ->required(),

                    TextInput::make('queue')
                        ->label('Target Queue')
                        ->default('default')
                        ->required(),

                    TextInput::make('message')
                        ->label('Custom Diagnostic Message')
                        ->default('Diagnostic job dispatched by developer')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $job = new TestQueueJob(
                        behavior: $data['behavior'],
                        message: $data['message']
                    );

                    $dispatcher = $job->onQueue($data['queue']);

                    if ($data['behavior'] === 'delayed') {
                        $dispatcher->delay(now()->addSeconds(15));
                    }

                    dispatch($dispatcher);

                    $this->refreshStats();

                    Notification::make()
                        ->title('Test job dispatched!')
                        ->body("Sent to queue '{$data['queue']}' with mode '{$data['behavior']}'.")
                        ->success()
                        ->send();
                }),

            Action::make('restartWorkers')
                ->label('Restart Workers')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Restart Queue Workers')
                ->modalDescription('This broadcasts a restart signal (SIGTERM) instructing active queue workers to gracefully finish their current job and terminate. Your process supervisor (e.g. systemd/supervisor) will automatically restart them.')
                ->action(function () {
                    Artisan::call('queue:restart');
                    $this->refreshStats();

                    Notification::make()
                        ->title('Worker restart signal sent')
                        ->body('Workers will restart as soon as their current job completes.')
                        ->success()
                        ->send();
                }),
        ];

        if (! empty($this->stats['has_horizon'])) {
            $actions[] = Action::make('openHorizon')
                ->label('Open Horizon Dashboard')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url('/horizon')
                ->openUrlInNewTab();
        }

        return $actions;
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.queue_manager');
    }

    public function getTitle(): string
    {
        return __('sidebar.queue_manager');
    }
}
