<?php

namespace Celios\Core\Filament\Pages;

use BackedEnum;
use Celios\Core\Models\Menu;
use Celios\Core\Models\Setting;
use Celios\Core\Services\ThemeInstallerService;
use Celios\Core\Services\ThemeManager;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Celios\Core\Filament\Traits\HasModuleToggle;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ThemeSettingsPage extends Page
{
    use HasModuleToggle;

    protected static ?string $slug = 'settings/themes';

    protected static string|null|BackedEnum $navigationIcon = 'heroicon-o-paint-brush';

    protected string $view = 'filament.pages.theme-settings';

    protected static ?int $navigationSort = 10;

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_system');
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.theme_settings');
    }

    public function getTitle(): string
    {
        return __('sidebar.theme_settings');
    }

    public function getAvailableThemes(): Collection
    {
        return app(ThemeManager::class)->getAvailableThemes();
    }

    public function getActiveThemeId(): string
    {
        return app(ThemeManager::class)->getActiveTheme()->getId();
    }

    public function mount(): void
    {
        $themeManager = app(ThemeManager::class);
        $locations = $themeManager->getLocationAssignments();

        $menuLocationsState = [];
        foreach ($themeManager->getMenuLocations() as $locKey => $locLabel) {
            $menuLocationsState[$locKey] = $locations[$locKey] ?? null;
        }

        $this->form->fill([
            'menu_locations' => $menuLocationsState,
            'admin_theme' => setting('admin_theme', 'ocean'),
            'frontend_theme' => setting('frontend_theme', 'ocean'),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('install_theme')
                ->label(__('themes.install_theme'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->modalHeading(__('themes.upload_theme'))
                ->modalDescription(__('themes.upload_theme_description'))
                ->schema([
                    FileUpload::make('theme_zip')
                        ->label(__('themes.theme_zip_label'))
                        ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed', 'application/octet-stream'])
                        ->disk('local')
                        ->directory('temp-themes')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $relativePath = $data['theme_zip'] ?? null;
                    if (! $relativePath) {
                        return;
                    }

                    $fullPath = Storage::disk('local')->path($relativePath);
                    $result = app(ThemeInstallerService::class)->installFromZip($fullPath);
                    @unlink($fullPath);

                    if ($result['success']) {
                        Notification::make()
                            ->title($result['message'])
                            ->success()
                            ->send();

                        $this->redirect(static::getUrl());
                    } else {
                        Notification::make()
                            ->title(__('themes.theme_installation_failed'))
                            ->body($result['message'])
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function activateTheme(string $themeId): void
    {
        $themeManager = app(ThemeManager::class);
        if ($themeManager->setActiveTheme($themeId)) {
            Notification::make()
                ->title(__('themes.theme_activated', ['theme' => $themeId]))
                ->success()
                ->send();

            $this->redirect(static::getUrl());
        }
    }

    public function canDeleteThemes(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        if (method_exists($user, 'hasRole') && ($user->hasRole('super_admin') || $user->hasRole('super-admin'))) {
            return true;
        }

        return $user->can('Delete:Theme') || $user->can('delete_themes') || $user->can('manage_themes');
    }

    public function deleteTheme(string $themeId): void
    {
        abort_unless($this->canDeleteThemes(), 403, __('themes.unauthorized_delete_theme'));

        $result = app(ThemeInstallerService::class)->deleteTheme($themeId);

        if ($result['success']) {
            Notification::make()
                ->title($result['message'])
                ->success()
                ->send();

            $this->redirect(static::getUrl());
        } else {
            Notification::make()
                ->title($result['message'])
                ->danger()
                ->send();
        }
    }

    public function form(Schema $schema): Schema
    {
        $themeManager = app(ThemeManager::class);

        // Dynamically build Select inputs for each menu location defined by the active theme
        $locationFields = [];
        $menuOptions = Menu::query()->pluck('name', 'id')->toArray();

        foreach ($themeManager->getMenuLocations() as $locKey => $locLabel) {
            $locationFields[] = Select::make("menu_locations.{$locKey}")
                ->label($locLabel)
                ->options($menuOptions)
                ->placeholder(__('themes.select_menu_placeholder'))
                ->helperText(__('themes.location_identifier_key', ['key' => $locKey]));
        }

        return $schema
            ->statePath('data')
            ->columns(1)
            ->components([
                Section::make(__('themes.navigation_slots'))
                    ->description(__('themes.navigation_slots_description', ['theme' => $themeManager->getActiveTheme()->getName()]))
                    ->schema([
                        Grid::make(2)
                            ->schema($locationFields),
                    ]),

                Section::make(__('themes.admin_palette'))
                    ->description(__('themes.admin_palette_description'))
                    ->schema([
                        Radio::make('admin_theme')
                            ->label(__('themes.select_admin_theme'))
                            ->options([
                                'ocean' => __('themes.admin_theme_ocean'),
                                'emerald' => __('themes.admin_theme_emerald'),
                                'midnight' => __('themes.admin_theme_midnight'),
                                'modern-builder' => __('themes.admin_theme_modern_builder'),
                                'ravangrad' => __('themes.admin_theme_ravangrad'),
                            ])
                            ->default('ocean')
                            ->required(),
                    ]),

                Section::make(__('themes.frontend_palette'))
                    ->description(__('themes.frontend_palette_description'))
                    ->schema([
                        Radio::make('frontend_theme')
                            ->label(__('themes.select_frontend_palette'))
                            ->options([
                                'ocean' => __('themes.frontend_theme_ocean'),
                                'emerald' => __('themes.frontend_theme_emerald'),
                                'midnight' => __('themes.frontend_theme_midnight'),
                                'modern-builder' => __('themes.frontend_theme_modern_builder'),
                                'ravangrad' => __('themes.frontend_theme_ravangrad'),
                            ])
                            ->default('ocean')
                            ->required(),
                    ]),
            ]);
    }

    public function save(): void
    {
        $formData = $this->form->getState();

        // 1. Save theme menu locations mapping
        $menuLocations = $formData['menu_locations'] ?? [];
        if (is_array($menuLocations)) {
            $cleanedLocations = [];
            foreach ($menuLocations as $locKey => $menuId) {
                if ($menuId) {
                    $cleanedLocations[$locKey] = (int) $menuId;
                }
            }

            Setting::updateOrCreate(
                ['key' => 'theme_menu_locations'],
                ['value' => json_encode($cleanedLocations), 'type' => 'json']
            );
            Cache::forget('setting.theme_menu_locations');
        }

        // 2. Save admin and frontend color tokens
        foreach (['admin_theme', 'frontend_theme'] as $colorKey) {
            Setting::updateOrCreate(
                ['key' => $colorKey],
                ['value' => (string) ($formData[$colorKey] ?? 'ocean'), 'type' => 'text']
            );
            Cache::forget("setting.{$colorKey}");
        }

        Notification::make()
            ->title(__('themes.settings_saved'))
            ->body(__('themes.settings_saved_description'))
            ->success()
            ->send();

        $this->redirect(static::getUrl());
    }
}
