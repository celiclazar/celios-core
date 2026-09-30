<?php

namespace Celios\Core\Http\Controllers;

use Celios\Core\Models\Document;
use Celios\Core\Models\DocumentCategory;
use Celios\Core\Models\DocumentDownloadLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request, ?string $locale = null)
    {
        $locale = $this->resolveLocale($locale);

        $categories = DocumentCategory::query()
            ->active()
            ->public()
            ->root()
            ->with(['children' => fn ($q) => $q->active()->public()])
            ->orderBy('order')
            ->get();

        $query = Document::query()
            ->published()
            ->visibleToUser(auth()->user())
            ->with('category');

        $search = $request->input('search');
        if (filled($search)) {
            $query->where(function ($q) use ($search, $locale) {
                $q->where("title->{$locale}", 'like', "%{$search}%")
                  ->orWhere("description->{$locale}", 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        return view('documents.index', compact('documents', 'categories', 'search', 'locale'));
    }

    public function category(Request $request, string $locale, string $category_slug)
    {
        $locale = $this->resolveLocale($locale);

        $category = DocumentCategory::query()
            ->active()
            ->where(function ($q) use ($category_slug, $locale) {
                $q->where("slug->{$locale}", $category_slug)
                  ->orWhere("slug->" . config('locales.default', 'sr'), $category_slug);
            })
            ->firstOrFail();

        // If category is marked internal, ensure user has access
        if ($category->is_internal) {
            $user = auth()->user();
            if (! $user || ! ($user->can('View:Document') || $user->hasRole(['super_admin', 'admin', 'panel_user']))) {
                abort(403, __('documents.unauthorized'));
            }
        }

        $categories = DocumentCategory::query()
            ->active()
            ->public()
            ->root()
            ->with(['children' => fn ($q) => $q->active()->public()])
            ->orderBy('order')
            ->get();

        // Include child category IDs if any
        $categoryIds = array_merge([$category->id], $category->children->pluck('id')->toArray());

        $documents = Document::query()
            ->published()
            ->whereIn('category_id', $categoryIds)
            ->visibleToUser(auth()->user())
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('documents.category', compact('category', 'documents', 'categories', 'locale'));
    }

    public function download(Request $request, ?string $locale = null, ?string $slug = null)
    {
        if ($slug === null && $locale !== null) {
            $slug = $locale;
            $locale = config('locales.default', 'sr');
        }

        $locale = $this->resolveLocale($locale);

        $document = Document::query()
            ->published()
            ->where(function ($q) use ($slug, $locale) {
                $q->where("slug->{$locale}", $slug)
                  ->orWhere("slug->" . config('locales.default', 'sr'), $slug);
            })
            ->firstOrFail();

        $user = auth()->user();
        if (! $document->canAccess($user)) {
            if (! $user) {
                return redirect()->guest(route('login'))
                    ->with('error', __('documents.login_to_download'));
            }
            abort(403, __('documents.unauthorized'));
        }

        $disk = Storage::disk('documents');
        if (! $disk->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        // Increment downloads counter
        $document->increment('downloads_count');

        // Audit log
        DocumentDownloadLog::create([
            'document_id' => $document->id,
            'user_id' => $user?->id,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'locale' => $locale,
            'downloaded_at' => now(),
        ]);

        $fileName = $document->file_name ?: basename($document->file_path);

        return $disk->download($document->file_path, $fileName);
    }

    public function preview(Request $request, ?string $locale = null, ?string $slug = null)
    {
        if ($slug === null && $locale !== null) {
            $slug = $locale;
            $locale = config('locales.default', 'sr');
        }

        $locale = $this->resolveLocale($locale);

        $document = Document::query()
            ->published()
            ->where(function ($q) use ($slug, $locale) {
                $q->where("slug->{$locale}", $slug)
                  ->orWhere("slug->" . config('locales.default', 'sr'), $slug);
            })
            ->firstOrFail();

        $user = auth()->user();
        if (! $document->canAccess($user)) {
            if (! $user) {
                return redirect()->guest(route('login'))
                    ->with('error', __('documents.login_to_download'));
            }
            abort(403, __('documents.unauthorized'));
        }

        $disk = Storage::disk('documents');
        if (! $disk->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        $fileName = $document->file_name ?: basename($document->file_path);

        return $disk->response($document->file_path, $fileName, [
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    public function adminDownload(Request $request, int $id)
    {
        $user = auth()->user();
        if (! $user || ! ($user->can('View:Document') || $user->hasRole(['super_admin', 'admin', 'panel_user']))) {
            abort(403);
        }

        $document = Document::findOrFail($id);
        $disk = Storage::disk('documents');

        if (! $disk->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        $fileName = $document->file_name ?: basename($document->file_path);

        return $disk->download($document->file_path, $fileName);
    }

    public function adminPreview(Request $request, int $id)
    {
        $user = auth()->user();
        if (! $user || ! ($user->can('View:Document') || $user->hasRole(['super_admin', 'admin', 'panel_user']))) {
            abort(403);
        }

        $document = Document::findOrFail($id);
        $disk = Storage::disk('documents');

        if (! $disk->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        $fileName = $document->file_name ?: basename($document->file_path);

        return $disk->response($document->file_path, $fileName, [
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    protected function resolveLocale(?string $locale): string
    {
        $available = config('locales.available', []);
        $locales = is_array(reset($available)) ? array_keys($available) : array_keys($available);

        if ($locale && in_array($locale, $locales)) {
            app()->setLocale($locale);
            session()->put('locale', $locale);
            return $locale;
        }

        return app()->getLocale();
    }
}
