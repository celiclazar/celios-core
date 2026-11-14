<?php

namespace Celios\Core\Filament\Widgets;

use Celios\Core\Filament\Resources\Activities\ActivityResource;
use Celios\Core\Filament\Traits\HasWidgetToggle;
use Filament\Widgets\Widget;
use Spatie\Activitylog\Models\Activity;

class ActivityLogWidget extends Widget
{
    use HasWidgetToggle;

    public const MODULE_KEY = 'activity_log';

    protected string $view = 'filament.widgets.activity-log-widget';

    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg' => 4,
    ];

    protected static ?int $sort = 6;

    protected function getViewData(): array
    {
        $activities = [];
        try {
            $dbActivities = Activity::with('causer')->latest()->take(5)->get();
            if ($dbActivities->isNotEmpty()) {
                foreach ($dbActivities as $act) {
                    try {
                        $url = ActivityResource::getUrl('view', ['record' => $act]);
                    } catch (\Throwable $e) {
                        $url = url('/admin/activities');
                    }

                    $activities[] = [
                        'title' => ($act->causer->name ?? 'Korisnik').' '.$act->description,
                        'subtitle' => $act->created_at->format('H:i').' • '.($act->log_name ?? 'Sistem'),
                        'dot' => 'bg-[#4474bf]',
                        'url' => $url,
                    ];
                }
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return [
            'activities' => $activities,
        ];
    }
}
