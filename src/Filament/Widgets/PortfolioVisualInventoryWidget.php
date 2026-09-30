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

        if (empty($items)) {
            $items = [
                [
                    'title' => 'VILA DEDINJE RESURS',
                    'subtitle' => '14 slika • Dodato pre 2h',
                    'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=600&q=80',
                    'url' => url('/admin/pages'),
                ],
                [
                    'title' => 'PLANINSKA LOŽA ZLATIBOR',
                    'subtitle' => 'Draft • 8 slika',
                    'image' => 'https://images.unsplash.com/photo-1510798831971-661eb04b3739?auto=format&fit=crop&w=600&q=80',
                    'url' => url('/admin/pages'),
                ],
                [
                    'title' => 'DORĆOL LOFT 04',
                    'subtitle' => 'Naslovni blok • Live',
                    'image' => 'https://images.unsplash.com/photo-1600565193348-f74bd3c7ccdf?auto=format&fit=crop&w=600&q=80',
                    'url' => url('/admin/pages'),
                ],
            ];
        }

        return [
            'items' => $items,
        ];
    }
}
