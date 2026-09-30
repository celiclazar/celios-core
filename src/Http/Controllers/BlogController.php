<?php

namespace Celios\Core\Http\Controllers;

use Celios\Core\Models\Category;
use Celios\Core\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request, ?string $locale = null)
    {
        $locale = $this->resolveLocale($locale);

        $search = $request->input('search');

        $posts = Post::query()
            ->published()
            ->with(['category', 'author', 'featuredImage'])
            ->when($search, function ($query, $search) use ($locale) {
                $query->where(function ($q) use ($search, $locale) {
                    $q->where("title->{$locale}", 'like', "%{$search}%")
                      ->orWhere("excerpt->{$locale}", 'like', "%{$search}%")
                      ->orWhere("body->{$locale}", 'like', "%{$search}%");
                });
            })
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->paginate(9)
            ->withQueryString();

        $categories = Category::query()
            ->visible()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('order')
            ->get();

        return view('blog.index', [
            'posts' => $posts,
            'categories' => $categories,
            'currentCategory' => null,
            'search' => $search,
            'locale' => $locale,
        ]);
    }

    public function category(Request $request, ...$args)
    {
        $locale = $request->route('locale');
        $categorySlug = $request->route('category_slug');

        // Fallback for positional parameters if route parameter name is not matched
        if ($categorySlug === null) {
            $filteredArgs = array_values(array_filter($args, fn ($a) => is_string($a) && filled($a)));
            $availableLocales = array_keys(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']));

            if (! empty($filteredArgs)) {
                if (in_array($filteredArgs[0], $availableLocales)) {
                    $locale = $filteredArgs[0];
                    $categorySlug = end($filteredArgs);
                } else {
                    $locale = config('locales.default', 'sr');
                    $categorySlug = end($filteredArgs);
                }
            }
        }

        $locale = $this->resolveLocale($locale);

        // Handle nested slug: e.g. "hrana/recepti" -> leaf slug "recepti"
        $slugSegments = explode('/', trim((string) $categorySlug, '/'));
        $leafSlug = end($slugSegments);

        $category = Category::query()
            ->where("slug->{$locale}", $leafSlug)
            ->first();

        if (! $category) {
            // Check full category_slug on current locale
            $category = Category::query()
                ->where("slug->{$locale}", $categorySlug)
                ->first();
        }

        if (! $category) {
            // Check other locales fallback
            foreach (config('locales.available', []) as $lang => $name) {
                $category = Category::query()->where("slug->{$lang}", $leafSlug)->first();
                if ($category) {
                    break;
                }
            }
        }

        abort_if(! $category, 404);

        $search = $request->input('search');

        $posts = Post::query()
            ->published()
            ->where('category_id', $category->id)
            ->with(['category', 'author', 'featuredImage'])
            ->when($search, function ($query, $search) use ($locale) {
                $query->where(function ($q) use ($search, $locale) {
                    $q->where("title->{$locale}", 'like', "%{$search}%")
                      ->orWhere("excerpt->{$locale}", 'like', "%{$search}%")
                      ->orWhere("body->{$locale}", 'like', "%{$search}%");
                });
            })
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->paginate(9)
            ->withQueryString();

        $categories = Category::query()
            ->visible()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('order')
            ->get();

        return view('blog.index', [
            'posts' => $posts,
            'categories' => $categories,
            'currentCategory' => $category,
            'search' => $search,
            'locale' => $locale,
        ]);
    }

    public function show(Request $request, ?string $locale = null, ?string $slug = null)
    {
        // Handle when route is hit without locale prefix (/blog/{slug})
        if ($slug === null && $locale !== null) {
            $slug = $locale;
            $locale = config('locales.default', 'sr');
        }

        $locale = $this->resolveLocale($locale);

        $post = Post::query()
            ->published()
            ->where("slug->{$locale}", $slug)
            ->with(['category', 'author', 'featuredImage'])
            ->first();

        if (! $post) {
            // Check other locales fallback
            foreach (config('locales.available', []) as $lang => $name) {
                $post = Post::query()->published()->where("slug->{$lang}", $slug)->with(['category', 'author', 'featuredImage'])->first();
                if ($post) {
                    break;
                }
            }
        }

        abort_if(! $post, 404);

        // Increment views
        $post->increment('views_count');

        // Related posts in same category
        $relatedPosts = Post::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->with(['category', 'author', 'featuredImage'])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(3)
            ->get();

        return view('blog.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'locale' => $locale,
        ]);
    }

    protected function resolveLocale(?string $locale): string
    {
        $availableLocales = array_keys(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']));
        $defaultLocale = config('locales.default', 'sr');

        if ($locale && in_array($locale, $availableLocales)) {
            app()->setLocale($locale);
            session()->put('locale', $locale);
            return $locale;
        }

        $sessionLocale = session('locale', $defaultLocale);
        app()->setLocale($sessionLocale);
        return $sessionLocale;
    }
}
