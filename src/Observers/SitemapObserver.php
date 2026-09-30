<?php

namespace Celios\Core\Observers;

use Celios\Core\Services\Sitemap\SitemapGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SitemapObserver
{
    /**
     * Handle model saved event.
     */
    public function saved(Model $model): void
    {
        $this->updateSitemap();
    }

    /**
     * Handle model deleted event.
     */
    public function deleted(Model $model): void
    {
        $this->updateSitemap();
    }

    /**
     * Handle model restored event.
     */
    public function restored(Model $model): void
    {
        $this->updateSitemap();
    }

    /**
     * Invalidate cache and optionally regenerate static file.
     */
    protected function updateSitemap(): void
    {
        try {
            $generator = app(SitemapGenerator::class);
            $generator->clearCache();

            if (config('sitemap.generate_static', true)) {
                $generator->writeToFile();
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to automatically regenerate sitemap: ' . $e->getMessage());
        }
    }
}
