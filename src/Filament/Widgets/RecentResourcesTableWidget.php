<?php

namespace Celios\Core\Filament\Widgets;

use Celios\Core\Filament\Resources\Pages\PageResource;
use Celios\Core\Filament\Resources\Posts\PostResource;
use Celios\Core\Models\Page;
use Celios\Core\Models\Post;
use Filament\Widgets\Widget;

class RecentResourcesTableWidget extends Widget
{
    protected string $view = 'filament.widgets.recent-resources-table-widget';

    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg' => 8,
    ];

    protected static ?int $sort = 3;

    public string $search = '';
    public string $filter = 'all'; // 'all', 'pages', 'posts'
    public int $page = 1;
    public int $perPage = 5;

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function updatedFilter(): void
    {
        $this->page = 1;
    }

    public function setFilter(string $filter): void
    {
        $this->filter = in_array($filter, ['all', 'pages', 'posts']) ? $filter : 'all';
        $this->page = 1;
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
        }
    }

    public function nextPage(int $totalPages): void
    {
        if ($this->page < $totalPages) {
            $this->page++;
        }
    }

    public function gotoPage(int $pageNumber): void
    {
        $this->page = max(1, $pageNumber);
    }

    protected function getViewData(): array
    {
        $items = collect();

        try {
            // 1. Pages
            if ($this->filter === 'all' || $this->filter === 'pages') {
                $pagesQuery = Page::query()->latest('updated_at');

                if (filled($this->search)) {
                    $term = '%' . trim($this->search) . '%';
                    $pagesQuery->where(function ($q) use ($term) {
                        $q->where('title', 'like', $term)
                          ->orWhere('slug', 'like', $term);
                    });
                }

                $pages = $pagesQuery->take(50)->get();

                foreach ($pages as $page) {
                    $title = $page->getTranslation('title', 'sr', false)
                        ?: $page->getTranslation('title', 'en', false)
                        ?: ($page->getTranslations('title')[config('locales.default', 'sr')] ?? null);

                    if (empty($title) && is_array($page->title)) {
                        $title = reset($page->title) ?: null;
                    }

                    $title = $title ?: 'Stranica #' . $page->id;

                    $slug = $page->getTranslation('slug', 'sr', false)
                        ?: $page->getTranslation('slug', 'en', false)
                        ?: ($page->getTranslations('slug')[config('locales.default', 'sr')] ?? '');

                    if (empty($slug) && is_array($page->slug)) {
                        $slug = reset($page->slug) ?: '';
                    }

                    try {
                        $editUrl = PageResource::getUrl('edit', ['record' => $page]);
                    } catch (\Throwable $e) {
                        $editUrl = url('/admin/pages/' . $page->id . '/edit');
                    }

                    try {
                        $publicUrl = $page->getUrl('sr');
                    } catch (\Throwable $e) {
                        $publicUrl = url('/' . ltrim($slug, '/'));
                    }

                    if (!empty($page->draft_content)) {
                        $status = 'U pregledu';
                        $statusType = 'review';
                    } elseif ($page->is_visible) {
                        $status = 'Objavljeno';
                        $statusType = 'published';
                    } else {
                        $status = 'Nacrt (Draft)';
                        $statusType = 'draft';
                    }

                    $items->push([
                        'id' => 'page-' . $page->id,
                        'title' => $title,
                        'url' => '/' . ltrim($slug, '/'),
                        'public_url' => $publicUrl,
                        'edit_url' => $editUrl,
                        'module' => 'Stranica',
                        'module_icon' => 'document',
                        'languages' => [
                            ['code' => 'SR', 'status' => filled($page->getTranslation('title', 'sr', false)) ? 'full' : 'missing'],
                            ['code' => 'EN', 'status' => filled($page->getTranslation('title', 'en', false)) ? 'full' : 'missing'],
                        ],
                        'status' => $status,
                        'status_type' => $statusType,
                        'icon_bg' => 'bg-[#dfe8ff] text-[#275ba5]',
                        'updated_at' => $page->updated_at,
                    ]);
                }
            }

            // 2. Posts (Blog)
            if ($this->filter === 'all' || $this->filter === 'posts') {
                $postsQuery = Post::query()->latest('updated_at');

                if (filled($this->search)) {
                    $term = '%' . trim($this->search) . '%';
                    $postsQuery->where(function ($q) use ($term) {
                        $q->where('title', 'like', $term)
                          ->orWhere('slug', 'like', $term);
                    });
                }

                $posts = $postsQuery->take(50)->get();

                foreach ($posts as $post) {
                    $title = $post->getTranslation('title', 'sr', false)
                        ?: $post->getTranslation('title', 'en', false)
                        ?: ($post->getTranslations('title')[config('locales.default', 'sr')] ?? null);

                    if (empty($title) && is_array($post->title)) {
                        $title = reset($post->title) ?: null;
                    }

                    $title = $title ?: 'Objava #' . $post->id;

                    $slug = $post->getTranslation('slug', 'sr', false)
                        ?: $post->getTranslation('slug', 'en', false)
                        ?: ($post->getTranslations('slug')[config('locales.default', 'sr')] ?? '');

                    if (empty($slug) && is_array($post->slug)) {
                        $slug = reset($post->slug) ?: '';
                    }

                    try {
                        $editUrl = PostResource::getUrl('edit', ['record' => $post]);
                    } catch (\Throwable $e) {
                        $editUrl = url('/admin/posts/' . $post->id . '/edit');
                    }

                    try {
                        $publicUrl = $post->getUrl('sr');
                    } catch (\Throwable $e) {
                        $publicUrl = url('/blog/' . ltrim($slug, '/'));
                    }

                    if ($post->is_published && ($post->published_at === null || $post->published_at->isPast())) {
                        $status = 'Objavljeno';
                        $statusType = 'published';
                    } else {
                        $status = 'Nacrt (Draft)';
                        $statusType = 'draft';
                    }

                    $items->push([
                        'id' => 'post-' . $post->id,
                        'title' => $title,
                        'url' => '/blog/' . ltrim($slug, '/'),
                        'public_url' => $publicUrl,
                        'edit_url' => $editUrl,
                        'module' => 'Blog',
                        'module_icon' => 'newspaper',
                        'languages' => [
                            ['code' => 'SR', 'status' => filled($post->getTranslation('title', 'sr', false)) ? 'full' : 'missing'],
                            ['code' => 'EN', 'status' => filled($post->getTranslation('title', 'en', false)) ? 'full' : 'missing'],
                        ],
                        'status' => $status,
                        'status_type' => $statusType,
                        'icon_bg' => 'bg-[#c1ecd5] text-[#088557]',
                        'updated_at' => $post->updated_at,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Log or ignore gracefully
        }

        // Sort combined items by updated_at descending
        $sorted = $items->sortByDesc(function ($item) {
            return $item['updated_at']?->timestamp ?? 0;
        })->values();

        // If no records exist in DB at all, use fallback mock items with clickable fallback URLs
        if ($sorted->isEmpty() && blank($this->search)) {
            $sorted = collect($this->getDefaultResources());
        }

        $totalCount = $sorted->count();
        $totalPages = max(1, (int) ceil($totalCount / $this->perPage));

        if ($this->page > $totalPages) {
            $this->page = $totalPages;
        }

        $pagedResources = $sorted->forPage($this->page, $this->perPage)->values();

        return [
            'resources' => $pagedResources,
            'totalCount' => $totalCount,
            'totalPages' => $totalPages,
            'currentPage' => $this->page,
            'currentFilter' => $this->filter,
        ];
    }

    protected function getDefaultResources(): array
    {
        return [
            [
                'id' => 'mock-1',
                'title' => 'Vila Dedinje — Rezidencijalni Kompleks',
                'url' => '/projekti/vila-dedinje',
                'public_url' => url('/'),
                'edit_url' => url('/admin/pages'),
                'module' => 'Portfolio',
                'module_icon' => 'briefcase',
                'languages' => [
                    ['code' => 'SR', 'status' => 'full'],
                    ['code' => 'EN', 'status' => 'full'],
                ],
                'status' => 'Objavljeno',
                'status_type' => 'published',
                'icon_bg' => 'bg-[#e8eeff] text-[#4474bf]',
                'updated_at' => now()->subHours(2),
            ],
            [
                'id' => 'mock-2',
                'title' => 'O Nama / O Birou',
                'url' => '/stranice/o-nama',
                'public_url' => url('/'),
                'edit_url' => url('/admin/pages'),
                'module' => 'Stranica',
                'module_icon' => 'document',
                'languages' => [
                    ['code' => 'SR', 'status' => 'full'],
                    ['code' => 'EN', 'status' => 'partial'],
                ],
                'status' => 'U pregledu',
                'status_type' => 'review',
                'icon_bg' => 'bg-[#dfe8ff] text-[#275ba5]',
                'updated_at' => now()->subHours(5),
            ],
            [
                'id' => 'mock-3',
                'title' => 'Održiva arhitektura i pasivna gradnja',
                'url' => '/blog/odrziva-arhitektura',
                'public_url' => url('/blog'),
                'edit_url' => url('/admin/posts'),
                'module' => 'Blog',
                'module_icon' => 'newspaper',
                'languages' => [
                    ['code' => 'SR', 'status' => 'full'],
                    ['code' => 'EN', 'status' => 'missing'],
                ],
                'status' => 'Nacrt (Draft)',
                'status_type' => 'draft',
                'icon_bg' => 'bg-[#c1ecd5] text-[#088557]',
                'updated_at' => now()->subDay(),
            ],
            [
                'id' => 'mock-4',
                'title' => 'Hero Sekcija sa Video Pozadinom',
                'url' => 'Blok: hero-full-cinematic',
                'public_url' => url('/'),
                'edit_url' => url('/admin/pages'),
                'module' => 'Blok',
                'module_icon' => 'grid',
                'languages' => [
                    ['code' => 'SR', 'status' => 'full'],
                    ['code' => 'EN', 'status' => 'full'],
                ],
                'status' => 'Objavljeno',
                'status_type' => 'published',
                'icon_bg' => 'bg-[#d6e3ff] text-[#00458d]',
                'updated_at' => now()->subDays(2),
            ],
        ];
    }
}
