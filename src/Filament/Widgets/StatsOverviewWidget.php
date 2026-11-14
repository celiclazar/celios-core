<?php

namespace Celios\Core\Filament\Widgets;

use Celios\Core\Filament\Traits\HasWidgetToggle;
use Celios\Core\Models\FormSubmission;
use Celios\Core\Models\Media;
use Celios\Core\Models\Page;
use Celios\Core\Models\Post;
use Celios\Core\Models\User;
use Filament\Widgets\Widget;

class StatsOverviewWidget extends Widget
{
    use HasWidgetToggle;

    protected string $view = 'filament.widgets.stats-overview-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        try {
            $pagesCount = Page::count();
            $postsCount = Post::count();
            $mediaCount = class_exists(Media::class) ? Media::count() : 0;
            $submissionsCount = class_exists(FormSubmission::class) ? FormSubmission::count() : 0;
            $usersCount = class_exists(User::class) ? User::count() : 1;

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
