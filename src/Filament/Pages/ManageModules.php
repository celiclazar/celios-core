<?php

namespace Celios\Core\Filament\Pages;

use Celios\Core\Services\ModuleManager;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageModules extends Page
{
    protected static ?string $slug = 'manage-modules';

    protected static string|null|BackedEnum $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_system');
    }

    protected string $view = 'filament.pages.manage-modules';

    public ?array $data = [];

    /**
     * Strict access: Only Superadmin can access this page.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && $user->hasRole('super_admin');
    }

    /**
     * Strict navigation: Only Superadmin sees this in sidebar.
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        return $user && $user->hasRole('super_admin');
    }

    public static function getNavigationLabel(): string
    {
        return 'Pricing Plans & Modules';
    }

    public function getTitle(): string
    {
        return 'Pricing Plans & Module Management';
    }

    public function mount(): void
    {
        $activeModules = ModuleManager::getActiveModules();
        $currentPlan = ModuleManager::getCurrentPlan();

        $this->form->fill([
            'plan' => $currentPlan,
            'modules' => $activeModules,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $planOptions = collect(ModuleManager::getAllPlans())->mapWithKeys(function ($plan, $key) {
            return [$key => "{$plan['name']} ({$plan['badge']}) — {$plan['description']}"];
        })->put('custom', 'Custom Selection — Select custom modules individually.')->toArray();

        $moduleOptions = collect(ModuleManager::getAllModules())->mapWithKeys(function ($module, $key) {
            return [$key => $module['name']];
        })->toArray();

        $moduleDescriptions = collect(ModuleManager::getAllModules())->mapWithKeys(function ($module, $key) {
            return [$key => $module['description']];
        })->toArray();

        return $schema
            ->statePath('data')
            ->columns(1)
            ->components([
                Section::make('Client Pricing Plan Preset')
                    ->description('Select a predefined tier to automatically enable the modules included in that plan.')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        Radio::make('plan')
                            ->label('Select Tier')
                            ->options($planOptions)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state !== 'custom') {
                                    $planModules = config("modules.plans.{$state}.modules", []);
                                    $set('modules', $planModules);
                                }
                            }),
                    ]),

                Section::make('Individual Module Toggles')
                    ->description('Toggle specific features on or off. Core features (Pages, Media Manager, SEO) are always active.')
                    ->icon('heroicon-o-squares-plus')
                    ->schema([
                        CheckboxList::make('modules')
                            ->label('Active Optional Modules')
                            ->options($moduleOptions)
                            ->descriptions($moduleDescriptions)
                            ->columns(2)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $currentPlan = $get('plan');
                                if ($currentPlan !== 'custom') {
                                    $presetModules = config("modules.plans.{$currentPlan}.modules", []);
                                    $selected = is_array($state) ? $state : [];
                                    sort($selected);
                                    sort($presetModules);
                                    if ($selected !== $presetModules) {
                                        $set('plan', 'custom');
                                    }
                                }
                            }),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $modules = $data['modules'] ?? [];
        $plan = $data['plan'] ?? 'custom';

        ModuleManager::setActiveModules($modules, $plan);

        Notification::make()
            ->title('CMS Modules & Pricing Plan updated.')
            ->body('Active modules and pricing tier have been reconfigured. Sidebar and page blocks have been synchronized.')
            ->success()
            ->send();

        $this->redirect(static::getUrl());
    }
}
