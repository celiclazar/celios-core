<?php

namespace Celios\Core\Filament\Widgets;

use Filament\Widgets\Widget;

class PortfolioVisualInventoryWidget extends Widget
{
    protected string $view = 'filament.widgets.portfolio-visual-inventory-widget';

    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg' => 8,
    ];

    protected static ?int $sort = 2;

    protected function getViewData(): array
    {
        $items = [];
        try {
            $postsWithImage = \Celios\Core\Models\Post::with('featuredImage')
                ->whereNotNull('featured_image_id')
                ->latest()
                ->take(3)
                ->get();

            foreach ($postsWithImage as $post) {
                if ($post->featuredImage && !empty($post->featuredImage->url)) {
                    $items[] = [
                        'title' => mb_strtoupper($post->getTranslation('title', 'sr', false) ?: $post->getTranslation('title', 'en', false) ?: 'BLOG OBJAVA'),
                        'subtitle' => ($post->is_published ? 'Live' : 'Draft') . ' • ' . ($post->created_at ? $post->created_at->diffForHumans() : ''),
                        'image' => $post->featuredImage->url,
                        'url' => \Celios\Core\Filament\Resources\Posts\PostResource::getUrl('edit', ['record' => $post]),
                    ];
                }
            }

            if (count($items) < 3) {
                $mediaItems = \Celios\Core\Models\Media::latest()->take(3 - count($items))->get();
                foreach ($mediaItems as $media) {
                    if (!empty($media->url)) {
                        $items[] = [
                            'title' => mb_strtoupper($media->title ?: $media->name ?: 'MEDIJ'),
                            'subtitle' => ($media->width && $media->height ? "{$media->width}x{$media->height} • " : '') . ($media->created_at ? $media->created_at->diffForHumans() : ''),
                            'image' => $media->url,
                            'url' => url('/admin/media'),
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return [
            'items' => $items,
        ];
    }
}
