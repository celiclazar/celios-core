<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Models\Form;
use Celios\Core\Models\FormSubmission;
use Celios\Core\Models\User;
use Celios\Core\Notifications\FormSubmissionConfirmationNotification;
use Celios\Core\Notifications\NewFormSubmissionNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FormController extends Controller
{
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
                'message' => $form->getTranslation('confirmation_message', $currentLocale, false) ?: 'Thank you for your submission.',
            ]);
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
                            $fieldRules[] = 'mimes:' . implode(',', $extensions);
                        }
                    }
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

        // 4. Process Uploaded Files
        $uploadedFiles = [];
        $processedData = $request->except(['_token', '_hp_name', '_hp_time']);

        foreach ($fields as $field) {
            $key = $field['key'] ?? null;
            if ($key && ($field['type'] ?? '') === 'file' && $request->hasFile($key)) {
                $file = $request->file($key);
                $path = $file->store('form-attachments/' . date('Y/m'), 'private');
                $uploadedFiles[] = [
                    'field' => $key,
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
                $processedData[$key] = $file->getClientOriginalName();
            }
        }

        // 5. Store Submission
        $submission = FormSubmission::create([
            'form_id' => $form->id,
            'data' => $processedData,
            'files' => $uploadedFiles,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'status' => 'new',
            'locale' => $currentLocale,
        ]);

        // 6. Notifications
        try {
            // Admin notification
            $recipients = $form->recipients ?? [];
            if (! empty($recipients)) {
                Notification::route('mail', $recipients)
                    ->notify(new NewFormSubmissionNotification($submission));
            }

            // Auto-responder confirmation to submitter
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

        $confirmationMessage = $form->getTranslation('confirmation_message', $currentLocale, false) ?: 'Thank you for your submission.';

        return response()->json([
            'success' => true,
            'message' => $confirmationMessage,
            'submission_id' => $submission->id,
        ], 201);
    }
}
