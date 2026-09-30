<?php

namespace Celios\Core\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Celios\Core\Filament\Traits\HasModuleToggle;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupManager extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use HasModuleToggle;

    public const MODULE_KEY = 'backups';

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-cloud-arrow-up';

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_system');
    }

    protected string $view = 'filament.pages.backup-manager';

    public array $backups = [];

    public function mount(): void
    {
        $this->loadBackups();
    }

    public function loadBackups(): void
    {
        $diskName = config('backup.backup.destination.disks')[0] ?? 'local';
        $disk = Storage::disk($diskName);
        $backupName = config('backup.backup.name') ?? 'Laravel';

        $allZipFiles = [];

        try {
            // 1. Try $backupName directory
            if ($disk->exists($backupName)) {
                $files = $disk->allFiles($backupName);
                foreach ($files as $file) {
                    if (str_ends_with(strtolower($file), '.zip')) {
                        $allZipFiles[$file] = [
                            'disk' => $diskName,
                            'path' => $file,
                        ];
                    }
                }
            }

            // 2. Also check all files on disk
            $rootFiles = $disk->allFiles();
            foreach ($rootFiles as $file) {
                if (str_ends_with(strtolower($file), '.zip')) {
                    $allZipFiles[$file] = [
                        'disk' => $diskName,
                        'path' => $file,
                    ];
                }
            }

            // 3. Check storage/app/Laravel or storage/app directly
            $directDirs = [
                storage_path('app/' . $backupName),
                storage_path('app/private/' . $backupName),
                storage_path('app'),
            ];

            foreach ($directDirs as $dir) {
                if (is_dir($dir)) {
                    $found = glob($dir . '/*.zip') ?: [];
                    foreach ($found as $fullPath) {
                        $baseName = basename($fullPath);
                        $relPath = $backupName . '/' . $baseName;
                        if (!isset($allZipFiles[$relPath])) {
                            $allZipFiles[$relPath] = [
                                'disk' => $diskName,
                                'path' => $relPath,
                                'full_path' => $fullPath,
                            ];
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // gracefully catch any filesystem read issues
        }

        $this->backups = collect($allZipFiles)
            ->map(function ($item, $path) use ($disk) {
                $existsOnDisk = $disk->exists($item['path']);
                $sizeInBytes = $existsOnDisk ? $disk->size($item['path']) : (isset($item['full_path']) && file_exists($item['full_path']) ? filesize($item['full_path']) : 0);
                $lastMod = $existsOnDisk ? $disk->lastModified($item['path']) : (isset($item['full_path']) && file_exists($item['full_path']) ? filemtime($item['full_path']) : time());

                $sizeMb = round($sizeInBytes / 1024 / 1024, 2);
                $sizeFormatted = $sizeMb >= 1 ? $sizeMb . ' MB' : round($sizeInBytes / 1024, 1) . ' KB';

                return [
                    'id' => md5($path),
                    'name' => basename($path),
                    'size' => $sizeFormatted,
                    'size_bytes' => $sizeInBytes,
                    'date' => date('d.m.Y H:i', $lastMod),
                    'time_ago' => \Carbon\Carbon::createFromTimestamp($lastMod)->diffForHumans(),
                    'raw_date' => $lastMod,
                    'path' => $item['path'],
                    'full_path' => $item['full_path'] ?? null,
                ];
            })
            ->sortByDesc('raw_date')
            ->values()
            ->toArray();
    }

    public function getStats(): array
    {
        $totalBackups = count($this->backups);
        $totalBytes = collect($this->backups)->sum('size_bytes');
        $totalMb = round($totalBytes / 1024 / 1024, 2);
        $totalSizeFormatted = $totalMb >= 1 ? $totalMb . ' MB' : round($totalBytes / 1024, 1) . ' KB';

        $latestBackup = !empty($this->backups) ? ($this->backups[0]['time_ago'] ?? $this->backups[0]['date']) : 'Nema kopija';

        return [
            'total_count' => $totalBackups,
            'total_size' => $totalSizeFormatted,
            'latest' => $latestBackup,
            'disk' => config('backup.backup.destination.disks')[0] ?? 'local',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('runDbBackup')
                ->label('Backup Baze (Brzi)')
                ->icon('heroicon-o-circle-stack')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Pokretanje sigurnosne kopije baze podataka')
                ->modalDescription('Ova radnja kreira SQL dump baze podataka i pakuje ga u zip arhivu.')
                ->modalSubmitActionLabel('Pokreni backup baze')
                ->action(fn () => $this->executeBackup(onlyDb: true)),

            Action::make('runFullBackup')
                ->label('Kompletan Backup (Fajlovi + Baza)')
                ->icon('heroicon-o-archive-box')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Pokretanje kompletne sigurnosne kopije')
                ->modalDescription('Ova radnja arhivira celu bazu podataka i sve fajlove aplikacije. Može potrajati par trenutaka.')
                ->modalSubmitActionLabel('Pokreni kompletan backup')
                ->action(fn () => $this->executeBackup(onlyDb: false)),
        ];
    }

    public function executeBackup(bool $onlyDb = true): void
    {
        try {
            $params = [
                '--disable-notifications' => true,
            ];

            if ($onlyDb) {
                $params['--only-db'] = true;
            }

            $exitCode = Artisan::call('backup:run', $params);

            $this->loadBackups();
            $this->dispatch('$refresh');

            if ($exitCode === 0) {
                Notification::make()
                    ->title($onlyDb ? 'Backup baze je uspešno kreiran!' : 'Kompletan backup je uspešno kreiran!')
                    ->success()
                    ->send();
            } else {
                $output = Artisan::output();
                Notification::make()
                    ->title('Backup nije uspeo')
                    ->body(Str::limit($output ?: 'Došlo je do greške prilikom kreiranja sigurnosne kopije.', 300))
                    ->danger()
                    ->persistent()
                    ->send();
            }
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Greška pri kreiranju backup-a')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): Collection => collect($this->backups))
            ->columns([
                TextColumn::make('name')
                    ->label('Naziv Arhive')
                    ->icon('heroicon-o-archive-box')
                    ->description(fn (array $record) => $record['time_ago'] ?? null)
                    ->searchable(),

                TextColumn::make('size')
                    ->label('Veličina')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('date')
                    ->label('Datum i Vreme')
                    ->icon('heroicon-o-calendar'),
            ])
            ->emptyStateHeading('Nema pronađenih rezervnih kopija')
            ->emptyStateDescription('Trenutno u sistemu ne postoji nijedna kreirana sigurnosna kopija. Kliknite na dugme ispod da kreirate prvi backup baze podataka.')
            ->emptyStateIcon('heroicon-o-cloud-arrow-up')
            ->emptyStateActions([
                Action::make('emptyStateRunBackup')
                    ->label('Kreiraj prvi backup baze')
                    ->icon('heroicon-o-circle-stack')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn () => $this->executeBackup(onlyDb: true)),
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Preuzmi')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function (array $record) {
                        $diskName = config('backup.backup.destination.disks')[0] ?? 'local';
                        $disk = Storage::disk($diskName);

                        if ($disk->exists($record['path'])) {
                            return $disk->download($record['path'], $record['name']);
                        }

                        if (!empty($record['full_path']) && file_exists($record['full_path'])) {
                            return response()->download($record['full_path'], $record['name']);
                        }

                        Notification::make()
                            ->title('Fajl nije pronađen')
                            ->body('Rezervna kopija više ne postoji na serveru.')
                            ->danger()
                            ->send();
                    }),

                Action::make('delete')
                    ->label('Obriši')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Brisanje sigurnosne kopije')
                    ->modalDescription('Da li ste sigurni da želite trajno da obrišete ovu rezervnu kopiju?')
                    ->action(function (array $record) {
                        $diskName = config('backup.backup.destination.disks')[0] ?? 'local';
                        $disk = Storage::disk($diskName);

                        if ($disk->exists($record['path'])) {
                            $disk->delete($record['path']);
                        }

                        if (!empty($record['full_path']) && file_exists($record['full_path'])) {
                            @unlink($record['full_path']);
                        }

                        $this->loadBackups();
                        $this->dispatch('$refresh');

                        Notification::make()
                            ->title('Rezervna kopija je obrisana.')
                            ->success()
                            ->send();
                    }),
            ])
            ->paginated(false);
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.backup_manager');
    }

    public function getTitle(): string
    {
        return __('sidebar.backup_manager');
    }
}

