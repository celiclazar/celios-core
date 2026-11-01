<?php

namespace Celios\Core\Http\Controllers;

use Celios\Core\Models\Form;
use Celios\Core\Models\FormSubmission;
use Celios\Core\Models\User;
use Celios\Core\Notifications\FormSubmissionConfirmationNotification;
use Celios\Core\Notifications\NewFormSubmissionNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FormSubmissionController extends Controller
{
    public function submit(Request $request, ?string $locale = null, ?string $slug = null): JsonResponse|RedirectResponse
    {
        // 1. Resolve Locale and Slug
        $targetSlug = $slug ?: $locale;
        $supportedLocales = ['sr', 'en', 'it'];

        if ($locale && in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
        }

        // 2. Find Active Form
        $form = Form::where('is_active', true)
            ->where(function ($query) use ($targetSlug) {
                $query->where('slug', $targetSlug)
                    ->orWhere('id', $targetSlug);
            })
            ->first();

        if (! $form) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('forms.submission_failed'),
                ], 404);
            }
            abort(404);
        }

        // 3. Spam Honeypot Verification
        // _hp_name must be empty (hidden field)
        if ($request->filled('_hp_name')) {
            // Fake success response to confuse spambots
            return $this->successResponse($request, $form, __('forms.submission_successful'));
        }

        // _hp_time check (submission took less than 1.5 seconds)
        if ($request->filled('_hp_time')) {
            $renderTime = (int) $request->input('_hp_time');
            if (time() - $renderTime < 2) {
                return $this->successResponse($request, $form, __('forms.submission_successful'));
            }
        }

        // 3.1 Bot & Spam Captcha Verification (Turnstile / reCAPTCHA)
        $captchaService = app(\Celios\Core\Services\Security\CaptchaService::class);
        if ($captchaService->isEnabled()) {
            $token = $request->input('cf-turnstile-response')
                ?: $request->input('g-recaptcha-response')
                ?: $request->input('_captcha_token');

            if (! $captchaService->verify($token, $request->ip())) {
                $captchaError = __('forms.captcha_failed', [], 'Spam verification failed. Please try again.');

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $captchaError,
                        'errors' => ['captcha' => [$captchaError]],
                    ], 422);
                }

                return redirect()->back()
                    ->withErrors(['captcha' => $captchaError])
                    ->withInput();
            }
        }

        // 4. Build Dynamic Validation Rules
        $fields = $form->fields ?? [];
        $rules = [];
        $customAttributes = [];
        $currentLocale = app()->getLocale();

        foreach ($fields as $field) {
            $key = $field['key'] ?? null;
            if (! $key) {
                continue;
            }

            $type = $field['type'] ?? 'text';
            $isRequired = ! empty($field['required']);
            $fieldRules = [$isRequired ? 'required' : 'nullable'];

            // Field Label for error messages
            $label = $key;
            if (isset($field['label'])) {
                $label = is_array($field['label'])
                    ? ($field['label'][$currentLocale] ?? $field['label']['sr'] ?? $field['label']['en'] ?? $key)
                    : (string) $field['label'];
            }
            $customAttributes[$key] = $label;

            switch ($type) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'tel':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:50';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'checkbox':
                    $fieldRules[] = is_array($request->input($key)) ? 'array' : 'nullable';
                    break;
                case 'file':
                    $fieldRules[] = 'file';
                    $fieldRules[] = 'max:20480'; // 20MB max
                    if (! empty($field['allowed_types'])) {
                        $extensions = array_filter(array_map('trim', explode(',', str_replace('.', '', $field['allowed_types']))));
                        if (! empty($extensions)) {
                            $fieldRules[] = 'mimes:'.implode(',', $extensions);
                        }
                    } else {
                        // Enforce safe default whitelist when form creator did not specify extensions
                        $fieldRules[] = 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,jpg,jpeg,png,webp,zip';
                    }
                    // Explicitly reject dangerous executable and script file extensions
                    $fieldRules[] = function ($attribute, $value, $fail) {
                        if ($value instanceof \Illuminate\Http\UploadedFile) {
                            $ext = strtolower($value->getClientOriginalExtension());
                            $dangerous = ['php', 'phtml', 'phar', 'exe', 'bat', 'sh', 'svg', 'html', 'htm', 'js', 'cmd', 'cgi', 'pl', 'py'];
                            if (in_array($ext, $dangerous, true)) {
                                $fail("Files with extension [{$ext}] are forbidden for security reasons.");
                            }
                        }
                    };
                    break;
                case 'textarea':
                case 'text':
                case 'select':
                case 'radio':
                default:
                    $fieldRules[] = 'string';
                    break;
            }

            $rules[$key] = $fieldRules;
        }

        $validator = Validator::make($request->all(), $rules, [], $customAttributes);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('forms.submission_failed'),
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 5. Scan and Store Files in Private Quarantine Disk
        $uploadedFiles = [];
        $scanner = app(\Celios\Core\Services\Security\FileScannerInterface::class);

        foreach ($fields as $field) {
            $key = $field['key'] ?? null;
            $type = $field['type'] ?? 'text';

            if ($type === 'file' && $request->hasFile($key)) {
                $file = $request->file($key);
                if ($file && $file->isValid()) {
                    // Antivirus & Malware scanning pipeline
                    try {
                        $scanner->scan($file);
                    } catch (\Celios\Core\Exceptions\MalwareDetectedException $e) {
                        \Illuminate\Support\Facades\Log::alert('[SECURITY_AUDIT] Malware blocked in form submission', [
                            'field' => $key,
                            'filename' => $file->getClientOriginalName(),
                            'virus' => $e->getVirusName() ?: $e->getMessage(),
                            'ip' => $request->ip(),
                        ]);

                        $malwareError = __('forms.malware_detected', [], 'The uploaded file failed security scanning (malware detected).');

                        if ($request->expectsJson() || $request->ajax()) {
                            return response()->json([
                                'success' => false,
                                'message' => $malwareError,
                                'errors' => [$key => [$malwareError]],
                            ], 422);
                        }

                        return redirect()->back()
                            ->withErrors([$key => $malwareError])
                            ->withInput();
                    }

                    $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
                    $storedName = \Illuminate\Support\Str::uuid().'.'.$ext;
                    $path = $file->storeAs('submissions/'.date('Y/m'), $storedName, 'submissions');
                    $uploadedFiles[$key] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'disk' => 'submissions',
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ];
                }
            }
        }

        // 6. Gather Submission Data
        $data = [];
        $excludedKeys = ['_token', '_hp_name', '_hp_time', '_method'];
        foreach ($fields as $field) {
            $key = $field['key'] ?? null;
            if (! $key || in_array($key, $excludedKeys)) {
                continue;
            }

            if ($field['type'] === 'file') {
                if (isset($uploadedFiles[$key])) {
                    $data[$key] = $uploadedFiles[$key]['original_name'] ?? basename($uploadedFiles[$key]['path'] ?? '');
                }
            } else {
                $data[$key] = $request->input($key);
            }
        }

        // 7. Save to Database
        $submission = $form->submissions()->create([
            'data' => $data,
            'files' => $uploadedFiles,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'is_read' => false,
        ]);

        // 8. Send Notifications
        $this->sendNotifications($form, $submission, $data);

        // 9. Return Response
        $successMessage = $form->getTranslation('success_message', $currentLocale, true) ?: __('forms.submission_successful');

        return $this->successResponse($request, $form, $successMessage);
    }

    protected function sendNotifications(Form $form, $submission, array $data = []): void
    {
        $adminNotification = new NewFormSubmissionNotification($submission);

        // A. Send email notifications to assigned system users and legacy recipient_emails
        try {
            $recipientUserIds = $form->recipient_user_ids ?? [];
            if (is_array($recipientUserIds) && ! empty($recipientUserIds)) {
                $users = User::whereIn('id', $recipientUserIds)->get();
                foreach ($users as $u) {
                    if ($u->email) {
                        Notification::route('mail', trim($u->email))->notify($adminNotification);
                    }
                }
            }

            $legacyEmails = $form->recipient_emails ?? [];
            if (is_array($legacyEmails)) {
                foreach ($legacyEmails as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        Notification::route('mail', trim($email))->notify($adminNotification);
                    }
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        // B. Send in-app database notification to authorized users / super admins
        try {
            $usersToNotify = User::query()
                ->where('status', 'active')
                ->get()
                ->filter(fn (User $user) => $form->canUserViewSubmissions($user));

            if ($usersToNotify->isNotEmpty()) {
                Notification::send($usersToNotify, $adminNotification);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        // C. Send Auto-Responder confirmation email to submitter if enabled
        if ($form->send_confirmation_email) {
            try {
                $submitterEmail = null;
                foreach ($form->fields ?? [] as $f) {
                    if (($f['type'] ?? '') === 'email' && ! empty($data[$f['key']])) {
                        $submitterEmail = $data[$f['key']];
                        break;
                    }
                }

                if (! $submitterEmail) {
                    foreach ($data as $val) {
                        if (is_string($val) && filter_var($val, FILTER_VALIDATE_EMAIL)) {
                            $submitterEmail = $val;
                            break;
                        }
                    }
                }

                if ($submitterEmail) {
                    Notification::route('mail', trim($submitterEmail))
                        ->notify(new FormSubmissionConfirmationNotification($submission));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    protected function successResponse(Request $request, Form $form, string $message): JsonResponse|RedirectResponse
    {
        $redirectUrl = $form->getResolvedRedirectUrl(app()->getLocale());

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => $redirectUrl,
            ]);
        }

        if ($redirectUrl) {
            return redirect()->away($redirectUrl)->with('success', $message);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Securely stream uploaded submission attachment to authorized staff/admin.
     */
    public function adminDownload(Request $request, int $id, string $key)
    {
        $user = auth()->user();
        if (! $user || ! ($user->hasAnyRole(['super_admin', 'super-admin', 'admin', 'panel_user']) || $user->can('View:FormSubmission'))) {
            abort(403, 'Unauthorized access to submission attachment.');
        }

        $submission = FormSubmission::findOrFail($id);
        $files = $submission->files ?? [];

        if (! isset($files[$key])) {
            abort(404, 'File not found in submission.');
        }

        $fileData = $files[$key];
        $filePath = is_array($fileData) ? ($fileData['path'] ?? null) : $fileData;
        $originalName = is_array($fileData) ? ($fileData['original_name'] ?? basename($filePath)) : basename($filePath);

        if (! $filePath) {
            abort(404, 'File path is missing.');
        }

        $diskName = is_array($fileData) && isset($fileData['disk']) ? $fileData['disk'] : 'submissions';
        $disk = Storage::disk($diskName);

        // Fallback for legacy submissions stored on public disk
        if (! $disk->exists($filePath) && Storage::disk('public')->exists($filePath)) {
            $disk = Storage::disk('public');
        }

        if (! $disk->exists($filePath)) {
            abort(404, 'File does not exist on storage disk.');
        }

        return $disk->download($filePath, $originalName);
    }
}
