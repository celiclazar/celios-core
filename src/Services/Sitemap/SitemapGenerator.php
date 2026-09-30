<?php

namespace Celios\Core\Services\Sitemap;

use Celios\Core\Models\Category;
use Celios\Core\Models\Page;
use Celios\Core\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SitemapGenerator
{
    /**
     * Generate or fetch the cached XML sitemap.
     */
    public function generateXml(bool $forceRefresh = false): string
    {
        if (! config('sitemap.enabled', true)) {
            return $this->buildEmptyXml();
        }

        $cacheKey = config('sitemap.cache_key', 'sitemap_xml_cache');
        $cacheTtl = config('sitemap.cache_ttl', 86400);

        if ($forceRefresh) {
            $this->clearCache();
        }

        if ($cacheTtl <= 0) {
            return $this->buildXml();
        }

        return Cache::remember($cacheKey, $cacheTtl, function () {
            return $this->buildXml();
        });
    }

    /**
     * Clear the cached sitemap XML.
     */
    public function clearCache(): void
    {
        Cache::forget(config('sitemap.cache_key', 'sitemap_xml_cache'));
    }

    /**
     * Write the sitemap to a static XML file (e.g. public/sitemap.xml).
     */
    public function writeToFile(?string $filePath = null): string
    {
        $path = $filePath ?: config('sitemap.static_path', public_path('sitemap.xml'));
        $directory = dirname($path);

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $xml = $this->generateXml(forceRefresh: true);
        File::put($path, $xml);

        return $path;
    }

    /**
     * Retrieve all collected URLs with metadata.
     */
    public function getUrls(): Collection
    {
        $urls = collect();

        $defaultLocale = config('locales.default', 'sr');
        $availableLocales = array_keys(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']));

        // 1. Static / Core Routes
        $this->collectStaticRoutes($urls, $availableLocales, $defaultLocale);

        // 2. CMS Pages
        if (config('sitemap.models.pages.enabled', true)) {
            $this->collectPages($urls, $availableLocales, $defaultLocale);
        }

        // 3. Blog Categories
        if (config('sitemap.models.categories.enabled', true) && module_enabled('blog')) {
            $this->collectCategories($urls, $availableLocales, $defaultLocale);
        }

        // 4. Blog Posts
        if (config('sitemap.models.posts.enabled', true) && module_enabled('blog')) {
            $this->collectPosts($urls, $availableLocales, $defaultLocale);
        }

        // 5. Custom URLs from config
        $this->collectCustomUrls($urls);

        // Filter out excluded patterns
        return $this->filterExcludedUrls($urls);
    }

    /**
     * Collect static / core routes like home and blog index.
     */
    protected function collectStaticRoutes(Collection $urls, array $availableLocales, string $defaultLocale): void
    {
        // Homepages
        $homePriority = (float) config('sitemap.static_routes.home.priority', 1.0);
        $homeChangefreq = (string) config('sitemap.static_routes.home.changefreq', 'daily');

        $homeAlternates = [];
        $homeAlternates[$defaultLocale] = url('/');
        foreach ($availableLocales as $locale) {
            if ($locale !== $defaultLocale) {
                $homeAlternates[$locale] = url("/{$locale}");
            }
        }
        $homeAlternates['x-default'] = url('/');

        // Add default homepage
        $urls->push([
            'type' => 'static',
            'title' => 'Home (' . strtoupper($defaultLocale) . ')',
            'loc' => url('/'),
            'lastmod' => now()->toIso8601String(),
            'changefreq' => $homeChangefreq,
            'priority' => number_format($homePriority, 1, '.', ''),
            'locale' => $defaultLocale,
            'alternates' => $homeAlternates,
            'images' => [],
        ]);

        // Add localized homepages
        foreach ($availableLocales as $locale) {
            if ($locale === $defaultLocale) {
                continue;
            }
            $urls->push([
                'type' => 'static',
                'title' => 'Home (' . strtoupper($locale) . ')',
                'loc' => url("/{$locale}"),
                'lastmod' => now()->toIso8601String(),
                'changefreq' => $homeChangefreq,
                'priority' => number_format($homePriority, 1, '.', ''),
                'locale' => $locale,
                'alternates' => $homeAlternates,
                'images' => [],
            ]);
        }

        // Blog Index
        if (module_enabled('blog')) {
            $blogPriority = (float) config('sitemap.static_routes.blog.priority', 0.8);
            $blogChangefreq = (string) config('sitemap.static_routes.blog.changefreq', 'daily');

            $blogAlternates = [];
            $blogAlternates[$defaultLocale] = url("/{$defaultLocale}/blog");
            foreach ($availableLocales as $locale) {
                if ($locale !== $defaultLocale) {
                    $blogAlternates[$locale] = url("/{$locale}/blog");
                }
            }
            $blogAlternates['x-default'] = url("/{$defaultLocale}/blog");

            foreach ($availableLocales as $locale) {
                $urls->push([
                    'type' => 'static',
                    'title' => 'Blog Index (' . strtoupper($locale) . ')',
                    'loc' => url("/{$locale}/blog"),
                    'lastmod' => now()->toIso8601String(),
                    'changefreq' => $blogChangefreq,
                    'priority' => number_format($blogPriority, 1, '.', ''),
                    'locale' => $locale,
                    'alternates' => $blogAlternates,
                    'images' => [],
                ]);
            }
        }
    }

    /**
     * Collect CMS Pages.
     */
    protected function collectPages(Collection $urls, array $availableLocales, string $defaultLocale): void
    {
        $priority = (float) config('sitemap.models.pages.priority', 0.8);
        $changefreq = (string) config('sitemap.models.pages.changefreq', 'weekly');

        $pages = Page::query()
            ->where('is_visible', true)
            ->get();

        foreach ($pages as $page) {
            $rawSlugs = $page->getTranslations('slug') ?: [];
            $rawTitles = $page->getTranslations('title') ?: [];

            // Build alternates map for this page
            $alternates = [];
            $urlsByLocale = [];

            foreach ($availableLocales as $locale) {
                $slug = $rawSlugs[$locale] ?? null;
                if (filled($slug)) {
                    $slugTrimmed = ltrim($slug, '/');

                    // If it's a home slug (e.g. 'home' or 'pocetna') on default locale, point to /
                    if (in_array(strtolower($slugTrimmed), ['home', 'pocetna', 'homepage']) && $locale === $defaultLocale) {
                        $pageUrl = url('/');
                    } else {
                        $pageUrl = url("/{$locale}/{$slugTrimmed}");
                    }

                    $alternates[$locale] = $pageUrl;
                    $urlsByLocale[$locale] = [
                        'url' => $pageUrl,
                        'slug' => $slugTrimmed,
                        'title' => $rawTitles[$locale] ?? ($rawTitles[$defaultLocale] ?? "Page #{$page->id}"),
                    ];
                }
            }

            if (! empty($alternates)) {
                $alternates['x-default'] = $alternates[$defaultLocale] ?? reset($alternates);
            }

            $lastmod = ($page->updated_at ?? $page->created_at ?? now())->toIso8601String();

            foreach ($urlsByLocale as $locale => $data) {
                // If this URL is already added as homepage, avoid duplicate
                if ($data['url'] === url('/') && $urls->contains('loc', url('/'))) {
                    continue;
                }

                $urls->push([
                    'type' => 'page',
                    'title' => $data['title'] . ' (' . strtoupper($locale) . ')',
                    'loc' => $data['url'],
                    'lastmod' => $lastmod,
                    'changefreq' => $changefreq,
                    'priority' => number_format($priority, 1, '.', ''),
                    'locale' => $locale,
                    'alternates' => $alternates,
                    'images' => [],
                    'model_id' => $page->id,
                ]);
            }
        }
    }

    /**
     * Collect Blog Categories.
     */
    protected function collectCategories(Collection $urls, array $availableLocales, string $defaultLocale): void
    {
        $priority = (float) config('sitemap.models.categories.priority', 0.6);
        $changefreq = (string) config('sitemap.models.categories.changefreq', 'weekly');

        $categories = Category::query()
            ->where('is_visible', true)
            ->get();

        foreach ($categories as $category) {
            $rawSlugs = $category->getTranslations('slug') ?: [];
            $rawTitles = $category->getTranslations('title') ?: [];

            $alternates = [];
            $urlsByLocale = [];

            foreach ($availableLocales as $locale) {
                $slug = $category->getNestedSlug($locale);
                if (filled($slug)) {
                    $catUrl = $category->getUrl($locale);

                    $alternates[$locale] = $catUrl;
                    $urlsByLocale[$locale] = [
                        'url' => $catUrl,
                        'slug' => $slug,
                        'title' => $rawTitles[$locale] ?? ($rawTitles[$defaultLocale] ?? "Category #{$category->id}"),
                    ];
                }
            }

            if (! empty($alternates)) {
                $alternates['x-default'] = $alternates[$defaultLocale] ?? reset($alternates);
            }

            $lastmod = ($category->updated_at ?? $category->created_at ?? now())->toIso8601String();

            foreach ($urlsByLocale as $locale => $data) {
                $urls->push([
                    'type' => 'category',
                    'title' => $data['title'] . ' (' . strtoupper($locale) . ')',
                    'loc' => $data['url'],
                    'lastmod' => $lastmod,
                    'changefreq' => $changefreq,
                    'priority' => number_format($priority, 1, '.', ''),
                    'locale' => $locale,
                    'alternates' => $alternates,
                    'images' => [],
                    'model_id' => $category->id,
                ]);
            }
        }
    }

    /**
     * Collect Blog Posts.
     */
    protected function collectPosts(Collection $urls, array $availableLocales, string $defaultLocale): void
    {
        $priority = (float) config('sitemap.models.posts.priority', 0.7);
        $changefreq = (string) config('sitemap.models.posts.changefreq', 'weekly');
        $includeImages = config('sitemap.include_images', true);

        $posts = Post::query()
            ->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->with(['featuredImage'])
            ->get();

        foreach ($posts as $post) {
            $rawSlugs = $post->getTranslations('slug') ?: [];
            $rawTitles = $post->getTranslations('title') ?: [];

            $alternates = [];
            $urlsByLocale = [];

            foreach ($availableLocales as $locale) {
                $slug = $rawSlugs[$locale] ?? null;
                if (filled($slug)) {
                    $slugTrimmed = ltrim($slug, '/');
                    $postUrl = url("/{$locale}/blog/{$slugTrimmed}");

                    $alternates[$locale] = $postUrl;
                    $urlsByLocale[$locale] = [
                        'url' => $postUrl,
                        'slug' => $slugTrimmed,
                        'title' => $rawTitles[$locale] ?? ($rawTitles[$defaultLocale] ?? "Post #{$post->id}"),
                    ];
                }
            }

            if (! empty($alternates)) {
                $alternates['x-default'] = $alternates[$defaultLocale] ?? reset($alternates);
            }

            $lastmod = ($post->updated_at ?? $post->published_at ?? $post->created_at ?? now())->toIso8601String();

            // Featured image extraction
            $images = [];
            if ($includeImages && $post->featuredImage) {
                $imageUrl = $post->featuredImage->url;
                if (filled($imageUrl)) {
                    $fullImageUrl = str_starts_with($imageUrl, 'http') ? $imageUrl : url($imageUrl);
                    $images[] = [
                        'loc' => $fullImageUrl,
                        'title' => $post->featuredImage->title ?? $post->featuredImage->alt ?? $post->getTranslation('title', $defaultLocale, false) ?? '',
                        'caption' => $post->featuredImage->caption ?? '',
                    ];
                }
            }

            foreach ($urlsByLocale as $locale => $data) {
                $urls->push([
                    'type' => 'post',
                    'title' => $data['title'] . ' (' . strtoupper($locale) . ')',
                    'loc' => $data['url'],
                    'lastmod' => $lastmod,
                    'changefreq' => $changefreq,
                    'priority' => number_format($priority, 1, '.', ''),
                    'locale' => $locale,
                    'alternates' => $alternates,
                    'images' => $images,
                    'model_id' => $post->id,
                ]);
            }
        }
    }

    /**
     * Collect custom URLs defined in configuration.
     */
    protected function collectCustomUrls(Collection $urls): void
    {
        $custom = config('sitemap.custom_urls', []);

        foreach ($custom as $item) {
            if (is_string($item)) {
                $item = ['url' => $item];
            }

            if (empty($item['url'])) {
                continue;
            }

            $urls->push([
                'type' => 'custom',
                'title' => $item['title'] ?? 'Custom URL',
                'loc' => str_starts_with($item['url'], 'http') ? $item['url'] : url($item['url']),
                'lastmod' => isset($item['lastmod']) ? Carbon::parse($item['lastmod'])->toIso8601String() : now()->toIso8601String(),
                'changefreq' => $item['changefreq'] ?? 'monthly',
                'priority' => number_format((float) ($item['priority'] ?? 0.5), 1, '.', ''),
                'locale' => $item['locale'] ?? config('locales.default', 'sr'),
                'alternates' => $item['alternates'] ?? [],
                'images' => $item['images'] ?? [],
            ]);
        }
    }

    /**
     * Filter out excluded URL patterns.
     */
    protected function filterExcludedUrls(Collection $urls): Collection
    {
        $excluded = config('sitemap.excluded_patterns', []);

        if (empty($excluded)) {
            return $urls->unique('loc')->values();
        }

        return $urls->reject(function ($item) use ($excluded) {
            $path = parse_url($item['loc'], PHP_URL_PATH) ?: '/';
            foreach ($excluded as $pattern) {
                if (Str::is($pattern, $path) || Str::is(ltrim($pattern, '/'), ltrim($path, '/'))) {
                    return true;
                }
            }
            return false;
        })->unique('loc')->values();
    }

    /**
     * Build the raw XML document.
     */
    public function buildXml(): string
    {
        $urls = $this->getUrls();
        $includeAlternates = config('sitemap.include_alternates', true);
        $includeImages = config('sitemap.include_images', true);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';

        if ($includeAlternates) {
            $xml .= "\n        xmlns:xhtml=\"http://www.w3.org/1999/xhtml\"";
        }
        if ($includeImages) {
            $xml .= "\n        xmlns:image=\"http://www.google.com/schemas/sitemap-image/1.1\"";
        }

        $xml .= ">\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";

            if (! empty($url['lastmod'])) {
                $xml .= "    <lastmod>" . htmlspecialchars($url['lastmod'], ENT_XML1, 'UTF-8') . "</lastmod>\n";
            }

            if (! empty($url['changefreq'])) {
                $xml .= "    <changefreq>" . htmlspecialchars($url['changefreq'], ENT_XML1, 'UTF-8') . "</changefreq>\n";
            }

            if (! empty($url['priority'])) {
                $xml .= "    <priority>" . htmlspecialchars($url['priority'], ENT_XML1, 'UTF-8') . "</priority>\n";
            }

            // Alternate hreflang tags
            if ($includeAlternates && ! empty($url['alternates']) && count($url['alternates']) > 1) {
                foreach ($url['alternates'] as $hreflang => $altHref) {
                    $xml .= '    <xhtml:link rel="alternate" hreflang="' . htmlspecialchars($hreflang, ENT_XML1, 'UTF-8') . '" href="' . htmlspecialchars($altHref, ENT_XML1, 'UTF-8') . '"/>' . "\n";
                }
            }

            // Image tags
            if ($includeImages && ! empty($url['images'])) {
                foreach ($url['images'] as $img) {
                    if (empty($img['loc'])) {
                        continue;
                    }
                    $xml .= "    <image:image>\n";
                    $xml .= "      <image:loc>" . htmlspecialchars($img['loc'], ENT_XML1, 'UTF-8') . "</image:loc>\n";
                    if (! empty($img['title'])) {
                        $xml .= "      <image:title>" . htmlspecialchars($img['title'], ENT_XML1, 'UTF-8') . "</image:title>\n";
                    }
                    if (! empty($img['caption'])) {
                        $xml .= "      <image:caption>" . htmlspecialchars($img['caption'], ENT_XML1, 'UTF-8') . "</image:caption>\n";
                    }
                    $xml .= "    </image:image>\n";
                }
            }

            $xml .= "  </url>\n";
        }

        $xml .= "</urlset>\n";

        return $xml;
    }

    /**
     * Fallback empty XML document when sitemap is disabled.
     */
    protected function buildEmptyXml(): string
    {
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n</urlset>\n";
    }

    /**
     * Retrieve statistics about the sitemap.
     */
    public function getStats(): array
    {
        $urls = $this->getUrls();
        $staticPath = config('sitemap.static_path', public_path('sitemap.xml'));
        $hasStaticFile = File::exists($staticPath);

        return [
            'total_urls' => $urls->count(),
            'pages_count' => $urls->where('type', 'page')->count(),
            'posts_count' => $urls->where('type', 'post')->count(),
            'categories_count' => $urls->where('type', 'category')->count(),
            'static_count' => $urls->where('type', 'static')->count(),
            'custom_count' => $urls->where('type', 'custom')->count(),
            'has_static_file' => $hasStaticFile,
            'static_path' => $staticPath,
            'static_size' => $hasStaticFile ? round(File::size($staticPath) / 1024, 2) . ' KB' : '0 KB',
            'static_modified' => $hasStaticFile ? Carbon::createFromTimestamp(File::lastModified($staticPath))->format('d.m.Y H:i:s') : null,
            'is_cached' => Cache::has(config('sitemap.cache_key', 'sitemap_xml_cache')),
            'sitemap_url' => url('/sitemap.xml'),
            'robots_url' => url('/robots.txt'),
        ];
    }
}
