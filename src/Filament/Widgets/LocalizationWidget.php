<?php

namespace Celios\Core\Filament\Widgets;

use Filament\Widgets\Widget;

class LocalizationWidget extends Widget
{
    protected string $view = 'filament.widgets.localization-widget';

    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg' => 4,
    ];

    protected static ?int $sort = 4;

    protected function getViewData(): array
    {
        try {
            $pages = \Celios\Core\Models\Page::all();
            $posts = \Celios\Core\Models\Post::all();
            $total = $pages->count() + $posts->count();

            if ($total > 0) {
                $srCount = 0;
                $enCount = 0;
                $pendingTitle = null;

                foreach ($pages as $p) {
                    $hasSr = filled($p->getTranslation('title', 'sr', false));
                    $hasEn = filled($p->getTranslation('title', 'en', false));
                    if ($hasSr) $srCount++;
                    if ($hasEn) {
                        $enCount++;
                    } elseif (!$pendingTitle) {
                        $pendingTitle = 'Stranica: "' . ($p->getTranslation('title', 'sr', false) ?: 'Stranica #' . $p->id) . '"';
                    }
                }

                foreach ($posts as $p) {
                    $hasSr = filled($p->getTranslation('title', 'sr', false));
                    $hasEn = filled($p->getTranslation('title', 'en', false));
                    if ($hasSr) $srCount++;
                    if ($hasEn) {
                        $enCount++;
                    } elseif (!$pendingTitle) {
                        $pendingTitle = 'Blog: "' . ($p->getTranslation('title', 'sr', false) ?: 'Objava #' . $p->id) . '"';
                    }
                }

                $enPct = round(($enCount / max(1, $total)) * 100, 1);
                $pendingCount = max(0, $total - $enCount);

                return [
                    'totalResources' => $total,
                    'serbianTranslated' => $srCount,
                    'englishTranslated' => $enCount,
                    'englishPercentage' => $enPct,
                    'pendingCount' => $pendingCount,
                    'pendingItem' => $pendingTitle ?: 'Svi resursi su prevedeni na engleski',
                ];
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return [
            'totalResources' => 0,
            'serbianTranslated' => 0,
            'englishTranslated' => 0,
            'englishPercentage' => 100,
            'pendingCount' => 0,
            'pendingItem' => 'Nema kreiranih stranica ili objava',
        ];
    }
}
