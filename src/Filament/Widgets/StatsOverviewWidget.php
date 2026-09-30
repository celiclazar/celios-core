<?php

namespace Celios\Core\Filament\Widgets;

use Celios\Core\Models\Page;
use Celios\Core\Models\Post;
use Filament\Widgets\Widget;

class StatsOverviewWidget extends Widget
{
    protected string $view = 'filament.widgets.stats-overview-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        try {
            $pagesCount = Page::count();
            $postsCount = Post::count();
            $mediaCount = class_exists(\Celios\Core\Models\Media::class) ? \Celios\Core\Models\Media::count() : 0;
            $submissionsCount = class_exists(\Celios\Core\Models\FormSubmission::class) ? \Celios\Core\Models\FormSubmission::count() : 0;
            $usersCount = class_exists(\Celios\Core\Models\User::class) ? \Celios\Core\Models\User::count() : 1;

            $pages = Page::all();
            $posts = Post::all();
            $totalItems = $pages->count() + $posts->count();
            $enTranslated = 0;

            foreach ($pages as $p) {
                if (filled($p->getTranslation('title', 'en', false))) {
                    $enTranslated++;
                }
            }
            foreach ($posts as $p) {
                if (filled($p->getTranslation('title', 'en', false))) {
                    $enTranslated++;
                }
            }

            $pendingTranslations = max(0, $totalItems - $enTranslated);
            $translatedPct = $totalItems > 0 ? round(($enTranslated / $totalItems) * 100, 0) : 100;
        } catch (\Throwable $e) {
            $pagesCount = 0;
            $postsCount = 0;
            $mediaCount = 0;
            $submissionsCount = 0;
            $usersCount = 1;
            $pendingTranslations = 0;
            $translatedPct = 100;
        }

        $totalContent = $pagesCount + $postsCount;

        return [
            'totalContent' => $totalContent,
            'pagesCount' => $pagesCount,
            'postsCount' => $postsCount,
            'mediaCount' => $mediaCount,
            'submissionsCount' => $submissionsCount,
            'usersCount' => $usersCount,
            'pendingTranslations' => $pendingTranslations,
            'translatedPercentage' => $translatedPct,
        ];
    }
}
