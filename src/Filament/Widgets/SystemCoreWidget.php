<?php

namespace Celios\Core\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\App;

class SystemCoreWidget extends Widget
{
    protected string $view = 'filament.widgets.system-core-widget';

    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg' => 4,
    ];

    protected static ?int $sort = 5;

    protected function getViewData(): array
    {
        $backupStatus = 'Nema kreiranih kopija';
        try {
            $diskName = config('backup.backup.destination.disks')[0] ?? 'local';
            $disk = \Illuminate\Support\Facades\Storage::disk($diskName);
            $backupName = config('backup.backup.name') ?? 'Laravel';

            $files = $disk->allFiles($backupName);
            if (empty($files)) {
                $files = collect($disk->allFiles())->filter(fn ($p) => str_ends_with(strtolower($p), '.zip'))->all();
            }

            if (!empty($files)) {
                $latestTime = 0;
                foreach ($files as $f) {
                    $latestTime = max($latestTime, $disk->lastModified($f));
                }
                if ($latestTime > 0) {
                    $backupStatus = \Carbon\Carbon::createFromTimestamp($latestTime)->diffForHumans();
                }
            }
        } catch (\Throwable $e) {
            // fallback
        }

        $dbDriver = config('database.default');
        $dbLabel = match($dbDriver) {
            'mysql' => 'MySQL 8.0',
            'pgsql' => 'PostgreSQL 16',
            'sqlite' => 'SQLite 3',
            default => ucfirst((string) $dbDriver),
        };

        return [
            'cmsVersion' => 'Celios v1.4.2',
            'adminPlatform' => 'Filament v3.2.115',
            'backend' => 'Laravel ' . App::version() . ' (PHP ' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . ')',
            'database' => $dbLabel,
            'activeTheme' => 'Svetla (Light Minimal)',
            'backupStatus' => $backupStatus,
        ];
    }
}
