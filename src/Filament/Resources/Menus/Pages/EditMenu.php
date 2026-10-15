<?php

namespace Celios\Core\Filament\Resources\Menus\Pages;

use Celios\Core\Filament\Resources\Menus\MenuResource;
use Celios\Core\Models\Setting;
use Celios\Core\Services\MenuResolverService;
use Celios\Core\Services\ThemeManager;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditMenu extends EditRecord
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn ($record) => $record?->isSystem()),
        ];
    }

    protected function afterSave(): void
    {
        $locations = $this->data['theme_locations'] ?? [];
        if (! is_array($locations)) {
            return;
        }

        $themeManager = app(ThemeManager::class);
        $currentSetting = $themeManager->getLocationAssignments();
        $themeLocations = $themeManager->getMenuLocations();
        $menuId = (int) $this->getRecord()->id;

        foreach ($themeLocations as $locKey => $locLabel) {
            if (in_array($locKey, $locations, true)) {
                $currentSetting[$locKey] = $menuId;
            } elseif (isset($currentSetting[$locKey]) && (int) $currentSetting[$locKey] === $menuId) {
                unset($currentSetting[$locKey]);
            }
        }

        Setting::updateOrCreate(
            ['key' => 'theme_menu_locations'],
            ['value' => json_encode($currentSetting), 'type' => 'json']
        );

        Cache::forget('setting.theme_menu_locations');
        app(MenuResolverService::class)->clearAllLocationCaches();
    }
}
