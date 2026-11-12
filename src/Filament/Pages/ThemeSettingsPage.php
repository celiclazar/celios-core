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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ThemeSettingsPage extends Page
{
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
            'active_theme' => $themeManager->getActiveTheme()->getId(),
            'menu_locations' => $menuLocationsState,
            'admin_theme' => setting('admin_theme', 'ocean'),
            'frontend_theme' => setting('frontend_theme', 'ocean'),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('install_theme')
                ->label('Install Theme (.zip)')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->modalHeading('Upload & Install Theme')
                ->modalDescription('Upload a .zip package containing theme.json and theme templates.')
                ->schema([
                    FileUpload::make('theme_zip')
                        ->label('Theme ZIP Package')
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
                            ->title('Theme installation failed')
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
                ->title("Theme '{$themeId}' activated successfully!")
                ->success()
                ->send();

            $this->redirect(static::getUrl());
        }
    }

    public function form(Schema $schema): Schema
    {
        $themeManager = app(ThemeManager::class);
        $themes = $themeManager->getAvailableThemes();
        $themeOptions = [];

        foreach ($themes as $id => $theme) {
            $themeOptions[$id] = "{$theme->getName()} (v{$theme->getVersion()}) — by {$theme->getAuthor()}: {$theme->getDescription()}";
        }

        // Dynamically build Select inputs for each menu location defined by the active theme
        $locationFields = [];
        $menuOptions = Menu::query()->pluck('name', 'id')->toArray();

        foreach ($themeManager->getMenuLocations() as $locKey => $locLabel) {
            $locationFields[] = Select::make("menu_locations.{$locKey}")
                ->label($locLabel)
                ->options($menuOptions)
                ->placeholder('— Select Menu for this Location —')
                ->helperText("Location identifier key: '{$locKey}'");
        }

        return $schema
            ->statePath('data')
            ->columns(1)
            ->components([
                Section::make('Active Website Theme')
                    ->description('Choose the active frontend view theme. Custom themes can override layouts, blocks, and styling.')
                    ->schema([
                        Radio::make('active_theme')
                            ->label('Select Installed Theme')
                            ->options($themeOptions)
                            ->required(),
                    ]),

                Section::make('Theme Navigation Locations (Slots)')
                    ->description('Assign which menus appear in the navigation slots defined by your active theme. No hardcoded template keys required.')
                    ->schema([
                        Grid::make(2)
                            ->schema($locationFields),
                    ]),

                Section::make('Admin Panel Palette')
                    ->description('Customize the visual appearance of your administration dashboard. Changes apply instantly.')
                    ->schema([
                        Radio::make('admin_theme')
                            ->label('Select Active Admin Theme')
                            ->options([
                                'ocean' => 'Ocean Blue — Corporate blue accents with clean slate surfaces (Default)',
                                'emerald' => 'Emerald Forest — Vivid emerald & mint accents with organic surfaces',
                                'midnight' => 'Midnight Obsidian — Ultra-modern violet & amber accents with deep slate/obsidian contrast',
                                'ravangrad' => 'Ravangrad Athletic Modernism — KSF Ravangrad crimson red & athletic stone surfaces',
                            ])
                            ->default('ocean')
                            ->required(),
                    ]),

                Section::make('Frontend Palette & Swatches')
                    ->description('Color variables and surface tokens for the default theme stylesheets.')
                    ->schema([
                        Radio::make('frontend_theme')
                            ->label('Select Active Frontend Color Swatch')
                            ->options([
                                'ocean' => 'Ocean Blue — Original brand styling with deep corporate navy and cobalt primary (Default)',
                                'emerald' => 'Emerald Forest — Modern fresh green tone with mint surfaces and sky secondary accents',
                                'midnight' => 'Midnight Obsidian — Sleek dark aesthetic with indigo/violet primary and zinc surfaces',
                                'ravangrad' => 'Ravangrad Athletic Modernism — KSF Ravangrad crimson red & athletic minimalism',
                            ])
                            ->default('ocean')
                            ->required(),
                    ]),
            ]);
    }

    public function save(): void
    {
        $formData = $this->form->getState();
        $themeManager = app(ThemeManager::class);

        // 1. Save active theme
        if (! empty($formData['active_theme'])) {
            $themeManager->setActiveTheme((string) $formData['active_theme']);
        }

        // 2. Save theme menu locations mapping
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

        // 3. Save admin and frontend color tokens
        foreach (['admin_theme', 'frontend_theme'] as $colorKey) {
            Setting::updateOrCreate(
                ['key' => $colorKey],
                ['value' => (string) ($formData[$colorKey] ?? 'ocean'), 'type' => 'text']
            );
            Cache::forget("setting.{$colorKey}");
        }

        Notification::make()
            ->title('Theme settings saved successfully!')
            ->body('Active theme and navigation locations have been updated.')
            ->success()
            ->send();

        $this->redirect(static::getUrl());
    }
}
