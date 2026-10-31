<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Http\Resources\V1\FormResource;
use Celios\Core\Http\Resources\V1\FormSubmissionResource;
use Celios\Core\Models\Form;
use Celios\Core\Models\FormSubmission;
use Celios\Core\Notifications\FormSubmissionConfirmationNotification;
use Celios\Core\Notifications\NewFormSubmissionNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FormController extends Controller
{
    /**
     * Get active form schema and field definitions by slug or ID.
     */
    public function show(string $slug): FormResource
    {
        $form = Form::where('is_active', true)
            ->where(function ($query) use ($slug) {
                $query->where('slug', $slug)
                    ->orWhere('id', is_numeric($slug) ? (int) $slug : 0);
            })
            ->firstOrFail();

        return new FormResource($form);
    }

    /**
     * Submit an active form by slug or ID.
     */
    public function submit(Request $request, string $slug): JsonResponse
    {
        $currentLocale = app()->getLocale();

        // 1. Find Active Form
        $form = Form::where('is_active', true)
            ->where(function ($query) use ($slug) {
                $query->where('slug', $slug)
                    ->orWhere('id', is_numeric($slug) ? (int) $slug : 0);
            })
            ->first();

        if (! $form) {
            return response()->json([
                'success' => false,
                'message' => 'Form not found or inactive.',
            ], 404);
        }

        // 2. Spam Honeypot verification
        if ($request->filled('_hp_name')) {
            return response()->json([
                'success' => true,
                'message' => $form->getTranslation('success_message', $currentLocale, false) ?: 'Thank you for your submission.',
            ]);
        }

        // 2.1 Bot & Spam Captcha Verification (Turnstile / reCAPTCHA)
        $captchaService = app(\Celios\Core\Services\Security\CaptchaService::class);
        if ($captchaService->isEnabled()) {
            $token = $request->input('cf-turnstile-response')
                ?: $request->input('g-recaptcha-response')
                ?: $request->input('_captcha_token');

            if (! $captchaService->verify($token, $request->ip())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Spam verification failed. Please try again.',
                    'errors' => ['captcha' => ['Spam verification failed. Please try again.']],
                ], 422);
            }
        }

        // 3. Build Dynamic Validation Rules
        $fields = $form->fields ?? [];
        $rules = [];
        $customAttributes = [];

        foreach ($fields as $field) {
            $key = $field['key'] ?? null;
            if (! $key) {
                continue;
            }

            $type = $field['type'] ?? 'text';
            $isRequired = ! empty($field['required']);
            $fieldRules = [$isRequired ? 'required' : 'nullable'];

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
                    $fieldRules[] = 'max:20480';
                    if (! empty($field['allowed_types'])) {
                        $extensions = array_filter(array_map('trim', explode(',', str_replace('.', '', $field['allowed_types']))));
                        if (! empty($extensions)) {
                            $fieldRules[] = 'mimes:'.implode(',', $extensions);
                        }
                    } else {
                        $fieldRules[] = 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,jpg,jpeg,png,webp,zip';
                    }
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
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // 4. Process Uploaded Files in Private Submissions Disk
        $uploadedFiles = [];
        $processedData = $request->except(['_token', '_hp_name', '_hp_time']);
        $scanner = app(\Celios\Core\Services\Security\FileScannerInterface::class);

        foreach ($fields as $field) {
            $key = $field['key'] ?? null;
            if ($key && ($field['type'] ?? '') === 'file' && $request->hasFile($key)) {
                $file = $request->file($key);
                if ($file && $file->isValid()) {
                    try {
                        $scanner->scan($file);
                    } catch (\Celios\Core\Exceptions\MalwareDetectedException $e) {
                        \Illuminate\Support\Facades\Log::alert('[SECURITY_AUDIT] Malware blocked in API form submission', [
                            'field' => $key,
                            'filename' => $file->getClientOriginalName(),
                            'virus' => $e->getVirusName() ?: $e->getMessage(),
                            'ip' => $request->ip(),
                        ]);

                        return response()->json([
                            'success' => false,
                            'message' => 'The uploaded file failed security scanning (malware detected).',
                            'errors' => [$key => ['The uploaded file failed security scanning (malware detected).']],
                        ], 422);
                    }

                    $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
                    $storedName = \Illuminate\Support\Str::uuid().'.'.$ext;
                    $path = $file->storeAs('submissions/'.date('Y/m'), $storedName, 'submissions');
                    $uploadedFiles[$key] = [
                        'field' => $key,
                        'original_name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'disk' => 'submissions',
                    ];
                    $processedData[$key] = $file->getClientOriginalName();
                }
            }
        }

        // 5. Store Submission
        $submission = FormSubmission::create([
            'form_id' => $form->id,
            'data' => $processedData,
            'files' => $uploadedFiles,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'is_read' => false,
        ]);

        // 6. Notifications
        try {
            $recipients = $form->recipient_emails ?? [];
            if (! empty($recipients)) {
                Notification::route('mail', $recipients)
                    ->notify(new NewFormSubmissionNotification($submission));
            }

            $senderEmail = null;
            foreach ($fields as $field) {
                if (($field['type'] ?? '') === 'email' && ! empty($processedData[$field['key']])) {
                    $senderEmail = $processedData[$field['key']];
                    break;
                }
            }

            if ($senderEmail && $form->send_confirmation_email) {
                Notification::route('mail', $senderEmail)
                    ->notify(new FormSubmissionConfirmationNotification($submission));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        $confirmationMessage = $form->getTranslation('success_message', $currentLocale, false) ?: 'Thank you for your submission.';

        return response()->json([
            'success' => true,
            'message' => $confirmationMessage,
            'submission_id' => $submission->id,
        ], 201);
    }

    /**
     * List all forms with submission counts (Admin).
     */
    public function index(): AnonymousResourceCollection
    {
        $forms = Form::query()
            ->withCount('submissions')
            ->latest('id')
            ->get();

        return FormResource::collection($forms);
    }

    /**
     * Create a new dynamic form (Admin).
     */
    public function store(Request $request): FormResource
    {
        $validated = $request->validate([
            'title' => ['required', 'array'],
            'slug' => ['required', 'string', 'max:255', 'unique:forms,slug'],
            'description' => ['nullable', 'array'],
            'fields' => ['required', 'array'],
            'recipient_emails' => ['nullable', 'array'],
            'send_confirmation_email' => ['boolean'],
            'confirmation_email_subject' => ['nullable', 'array'],
            'confirmation_email_body' => ['nullable', 'array'],
            'success_message' => ['nullable', 'array'],
            'submit_button_text' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ]);

        $form = Form::create($validated);

        return new FormResource($form);
    }

    /**
     * Update an existing dynamic form (Admin).
     */
    public function update(Request $request, Form $form): FormResource
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'array'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('forms', 'slug')->ignore($form->id)],
            'description' => ['nullable', 'array'],
            'fields' => ['sometimes', 'required', 'array'],
            'recipient_emails' => ['nullable', 'array'],
            'send_confirmation_email' => ['boolean'],
            'confirmation_email_subject' => ['nullable', 'array'],
            'confirmation_email_body' => ['nullable', 'array'],
            'success_message' => ['nullable', 'array'],
            'submit_button_text' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ]);

        $form->update($validated);

        return new FormResource($form);
    }

    /**
     * Delete a dynamic form (Admin).
     */
    public function destroy(Form $form): JsonResponse
    {
        $form->delete();

        return response()->json([
            'message' => 'Form deleted successfully.',
        ]);
    }

    /**
     * List paginated submissions for a form (Admin).
     */
    public function submissions(Request $request, Form $form): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 50);

        $submissions = $form->submissions()
            ->paginate($perPage)
            ->withQueryString();

        return FormSubmissionResource::collection($submissions);
    }

    /**
     * Mark a form submission as read/unread (Admin).
     */
    public function markSubmissionRead(Request $request, FormSubmission $submission): FormSubmissionResource
    {
        $submission->update([
            'is_read' => $request->boolean('is_read', true),
        ]);

        return new FormSubmissionResource($submission);
    }

    /**
     * Delete a form submission (Admin).
     */
    public function destroySubmission(FormSubmission $submission): JsonResponse
    {
        $submission->delete();

        return response()->json([
            'message' => 'Submission deleted successfully.',
        ]);
    }
}
