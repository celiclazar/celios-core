<?php

namespace Celios\Core\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification as FilamentNotification;

class Notifications extends Page
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-bell';
    protected string $view = 'filament.pages.notifications';

    public static function getNavigationLabel(): string
    {
        return __('sidebar.notifications');
    }

    public function getTitle(): string
    {
        return __('sidebar.notifications');
    }

    public function getNotificationsProperty()
    {
        return auth()->user()->notifications()->paginate(10);
    }

    public function markAsRead($notificationId)
    {
        $notification = auth()->user()->unreadNotifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();

            FilamentNotification::make()
                ->title(__('Uspšno označeno kao pročitano'))
                ->success()
                ->send();
        }
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        FilamentNotification::make()
            ->title(__('Sva obaveštenja su označena kao pročitana'))
            ->success()
            ->send();
    }
}
