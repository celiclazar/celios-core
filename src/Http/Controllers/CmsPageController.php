<?php

namespace Celios\Core\Http\Controllers;

use Celios\Core\Enums\PageType;
use Celios\Core\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class CmsPageController extends Controller
{
    public function home()
    {
        $defaultLocale = config('locales.default', 'sr');
        return $this->render($defaultLocale, 'home');
    }

    public function fallback(Request $request)
    {
        $path = trim($request->path(), '/');
        return $this->render(null, $path);
    }

    public function render($locale = null, $slug = null)
    {
        $supportedLocales = ['sr', 'en', 'it'];
        $page = null;

        // KORAK 1: Ako je eksplicitno prosleđen jezik kao prvi parametar (npr. /en/test ili /en)
        if (in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
            $currentLocale = $locale;

            if (empty($slug) || $slug === '/' || $slug === 'home') {
                $page = Page::where('is_visible', true)
                    ->where(function ($query) use ($currentLocale) {
                        $query->where('type', PageType::HOME->value)
                            ->orWhere("slug->{$currentLocale}", 'home');
                    })
                    ->first();
            } else {
                $page = Page::where('is_visible', true)
                    ->where("slug->{$currentLocale}", $slug)
                    ->first();
            }
        }

        // KORAK 2: Ako jezik NIJE prosleđen (npr. samo /test ili /proba) OR prva pretraga nije uspela
        if (!$page) {
            $targetSlug = $slug ?: $locale;

            if (empty($targetSlug) || $targetSlug === '/' || $targetSlug === 'home') {
                $page = Page::where('is_visible', true)
                    ->where('type', PageType::HOME->value)
                    ->first();
            }

            if (!$page) {
                // PAMETNA PRETRAGA: Tražimo slug kroz SVE jezike u bazi odjednom
                $page = Page::where('is_visible', true)
                    ->where(function ($query) use ($targetSlug, $supportedLocales) {
                        foreach ($supportedLocales as $lang) {
                            $query->orWhere("slug->{$lang}", $targetSlug);
                        }
                    })
                    ->firstOrFail();

                // KORAK 3: Kada pronađemo stranicu, detektujemo na kom jeziku je pronađen slug
                // i prebacujemo ceo sajt na taj jezik!
                foreach ($supportedLocales as $lang) {
                    if ($page->getTranslation('slug', $lang) === $targetSlug) {
                        App::setLocale($lang);
                        break;
                    }
                }
            }
        }

        // KORAK 4: Preview logika i slanje u Blade
        $activeRevision = null;
        $pageContent = $page->content;

        if (request()->has('preview') && auth()->check()) {
            if (request()->filled('revision_id')) {
                $activeRevision = $page->revisions()->with('user')->find(request('revision_id'));
                if ($activeRevision) {
                    $pageContent = $activeRevision->content;
                }
            } else {
                $pageContent = $page->draft_content ?? $page->content;
            }
        }

        return view('cms.render', [
            'page' => $page,
            'content' => $pageContent,
            'activeRevision' => $activeRevision,
        ]);
    }
}
