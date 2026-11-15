<?php

use Celios\Core\Http\Controllers\BlogController;
use Celios\Core\Http\Controllers\CmsPageController;
use Celios\Core\Http\Controllers\DocumentController;
use Celios\Core\Http\Controllers\FormSubmissionController;
use Celios\Core\Http\Controllers\NewsletterController;
use Celios\Core\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. LANGUAGE SWITCHER
// ==========================================
Route::get('/language/{locale}', function ($locale) {
    $locales = function_exists('active_locale_codes')
        ? active_locale_codes()
        : array_keys(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']));

    if (in_array($locale, $locales, true)) {
        session()->put('locale', $locale);
    }

    return redirect()->back();
})->name('language.switch');

// ==========================================
// 2. BLOG
// ==========================================
Route::middleware('module:blog')->group(function () {
    Route::get('/{locale}/blog', [BlogController::class, 'index'])
        ->where('locale', 'sr|en|it')
        ->name('blog.index.localized');

    Route::get('/{locale}/{category_prefix}/{category_slug}', [BlogController::class, 'category'])
        ->where('locale', 'sr|en|it')
        ->where('category_prefix', 'kategorije|categories|categorie')
        ->where('category_slug', '.*')
        ->name('blog.category.localized_prefix');

    Route::get('/{locale}/blog/{category_prefix}/{category_slug}', [BlogController::class, 'category'])
        ->where('locale', 'sr|en|it')
        ->where('category_prefix', 'kategorije|categories|categorie')
        ->where('category_slug', '.*')
        ->name('blog.category.localized_sub');

    Route::get('/{locale}/blog/category/{category_slug}', [BlogController::class, 'category'])
        ->where('locale', 'sr|en|it')
        ->where('category_slug', '.*')
        ->name('blog.category.localized');

    Route::get('/{locale}/blog/{slug}', [BlogController::class, 'show'])
        ->where('locale', 'sr|en|it')
        ->name('blog.show.localized');

    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');

    Route::get('/kategorije/{category_slug}', [BlogController::class, 'category'])
        ->where('category_slug', '.*')
        ->name('blog.category.sr');

    Route::get('/categories/{category_slug}', [BlogController::class, 'category'])
        ->where('category_slug', '.*')
        ->name('blog.category.en');

    Route::get('/categorie/{category_slug}', [BlogController::class, 'category'])
        ->where('category_slug', '.*')
        ->name('blog.category.it');

    Route::get('/blog/category/{category_slug}', [BlogController::class, 'category'])
        ->where('category_slug', '.*')
        ->name('blog.category');

    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
});

// ==========================================
// 3. SITEMAP & SEO
// ==========================================
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('sitemap.robots');

// ==========================================
// 4. FORM SUBMISSIONS
// ==========================================
Route::middleware('module:forms')->group(function () {
    Route::post('/{locale}/forms/{slug}/submit', [FormSubmissionController::class, 'submit'])
        ->where('locale', 'sr|en|it')
        ->middleware('throttle:form-submissions')
        ->name('forms.submit.localized');

    Route::post('/forms/{slug}/submit', [FormSubmissionController::class, 'submit'])
        ->middleware('throttle:form-submissions')
        ->name('forms.submit');

    Route::get('/admin/form-submissions/{id}/download/{key}', [FormSubmissionController::class, 'adminDownload'])
        ->middleware(['web', 'auth'])
        ->name('admin.form-submissions.download');
});

// ==========================================
// 5. NEWSLETTER
// ==========================================
Route::middleware('module:newsletter')->group(function () {
    Route::post('/{locale}/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
        ->where('locale', 'sr|en|it')
        ->middleware('throttle:newsletter-subscribe')
        ->name('newsletter.subscribe.localized');

    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
        ->middleware('throttle:newsletter-subscribe')
        ->name('newsletter.subscribe');

    Route::get('/{locale}/newsletter/verify/{token}', [NewsletterController::class, 'verify'])
        ->where('locale', 'sr|en|it')
        ->name('newsletter.verify.localized');

    Route::get('/newsletter/verify/{token}', [NewsletterController::class, 'verify'])
        ->name('newsletter.verify');

    Route::match(['get', 'post'], '/{locale}/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])
        ->where('locale', 'sr|en|it')
        ->name('newsletter.unsubscribe.localized');

    Route::match(['get', 'post'], '/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])
        ->name('newsletter.unsubscribe');

    Route::post('/newsletter/unsubscribe-rfc/{token}', [NewsletterController::class, 'unsubscribeRfc'])
        ->name('newsletter.unsubscribe.rfc');

    Route::get('/newsletter/track/open/{token}', [NewsletterController::class, 'trackOpen'])
        ->name('newsletter.track.open');

    Route::get('/newsletter/track/click/{token}', [NewsletterController::class, 'trackClick'])
        ->name('newsletter.track.click');
});

// ==========================================
// 6. DOCUMENTS
// ==========================================
Route::middleware('module:documents')->group(function () {
    Route::get('/{locale}/documents', [DocumentController::class, 'index'])
        ->where('locale', 'sr|en|it')
        ->name('documents.index.localized');

    Route::get('/{locale}/documents/category/{category_slug}', [DocumentController::class, 'category'])
        ->where('locale', 'sr|en|it')
        ->name('documents.category.localized');

    Route::get('/{locale}/documents/{slug}/download', [DocumentController::class, 'download'])
        ->where('locale', 'sr|en|it')
        ->name('documents.download.localized');

    Route::get('/documents/{document}/download-signed', [DocumentController::class, 'signedDownload'])
        ->middleware(['web', 'signed'])
        ->name('documents.download.signed');

    Route::get('/{locale}/documents/{slug}/preview', [DocumentController::class, 'preview'])
        ->where('locale', 'sr|en|it')
        ->name('documents.preview.localized');

    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/category/{category_slug}', [DocumentController::class, 'category'])->name('documents.category');
    Route::get('/documents/{slug}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{slug}/preview', [DocumentController::class, 'preview'])->name('documents.preview');

    // Admin direct actions
    Route::get('/admin/documents/{id}/download', [DocumentController::class, 'adminDownload'])
        ->middleware(['web', 'auth'])
        ->name('documents.admin.download');

    Route::get('/admin/documents/{id}/preview', [DocumentController::class, 'adminPreview'])
        ->middleware(['web', 'auth'])
        ->name('documents.admin.preview');
});

// ==========================================
// 7. SOCIAL OAUTH AUTHENTICATION
// ==========================================
Route::middleware('web')->group(function () {
    Route::get('/auth/{provider}/redirect', [\Celios\Core\Http\Controllers\SocialAuthController::class, 'redirect'])
        ->name('auth.social.redirect');
    Route::get('/auth/{provider}/callback', [\Celios\Core\Http\Controllers\SocialAuthController::class, 'callback'])
        ->name('auth.social.callback');
});

// ==========================================
// 8. CMS PAGES & FALLBACK
// ==========================================
Route::get('/', [CmsPageController::class, 'home'])->name('cms.home');
Route::get('/{locale}/{slug?}', [CmsPageController::class, 'render'])
    ->where('locale', 'sr|en|it')
    ->name('cms.page.localized');
Route::fallback([CmsPageController::class, 'fallback'])->name('cms.page');
