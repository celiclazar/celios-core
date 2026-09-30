<?php

namespace Celios\Core\Observers;

use Celios\Core\Models\MenuItem;
use Celios\Core\Services\MenuResolverService;

class MenuItemObserver
{
    public function saved(MenuItem $menuItem): void
    {
        $this->clearMenuCache($menuItem);
    }

    public function deleted(MenuItem $menuItem): void
    {
        $this->clearMenuCache($menuItem);
    }

    private function clearMenuCache(MenuItem $menuItem): void
    {
        if ($menuItem->menu) {
            app(MenuResolverService::class)->clearCache($menuItem->menu->key);
        }
    }
}
