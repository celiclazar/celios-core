<?php

namespace Celios\Core\Filament\Resources\Menus\Pages;

use Celios\Core\Filament\Resources\Menus\MenuResource;
use Celios\Core\Models\Setting;
use Celios\Core\Services\MenuResolverService;
use Celios\Core\Services\ThemeManager;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Cache;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;

    protected function afterCreate(): void
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
