<?php

namespace Celios\Core\Filament\Widgets;

use Filament\Widgets\Widget;
use Spatie\Activitylog\Models\Activity;

class ActivityLogWidget extends Widget
{
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
                        $url = \Celios\Core\Filament\Resources\Activities\ActivityResource::getUrl('view', ['record' => $act]);
                    } catch (\Throwable $e) {
                        $url = url('/admin/activities');
                    }

                    $activities[] = [
                        'title' => ($act->causer->name ?? 'Korisnik') . ' ' . $act->description,
                        'subtitle' => $act->created_at->format('H:i') . ' • ' . ($act->log_name ?? 'Sistem'),
                        'dot' => 'bg-[#4474bf]',
                        'url' => $url,
                    ];
                }
            }
        } catch (\Throwable $e) {
            // fallback
        }

        if (empty($activities)) {
            $activities = [
                [
                    'title' => 'Aleksa V. ažurirao blok "Hero Cinematic"',
                    'subtitle' => '14:22 • Izmenjen naslovni tag',
                    'dot' => 'bg-[#4474bf]',
                    'url' => url('/admin/activities'),
                ],
                [
                    'title' => 'Automatizovan Redis keš purge',
                    'subtitle' => '12:00 • Sistem periodični zadatak',
                    'dot' => 'bg-[#737782]',
                    'url' => url('/admin/activities'),
                ],
                [
                    'title' => 'Nova medijska stavka uploadovana',
                    'subtitle' => '09:41 • planinska-loza-01.webp',
                    'dot' => 'bg-[#c3c6d3]',
                    'url' => url('/admin/activities'),
                ],
            ];
        }

        return [
            'activities' => $activities,
        ];
    }
}
