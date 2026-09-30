<?php

namespace Celios\Core\Notifications;

use Celios\Core\Models\FormSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FormSubmissionConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public FormSubmission $submission)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $form = $this->submission->form;
        $locale = app()->getLocale();
        $formTitle = $form ? ($form->getTranslation('title', $locale, true) ?: $form->slug) : 'Form';

        $customSubject = $form?->getTranslation('confirmation_email_subject', $locale, true);
        $customBody = $form?->getTranslation('confirmation_email_body', $locale, true);

        $subject = filled($customSubject)
            ? $customSubject
            : __('forms.confirmation_email_default_subject', ['form' => $formTitle]);

        $greeting = __('forms.confirmation_email_default_greeting');

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting);

        if (filled($customBody)) {
            $mail->line($customBody);
        } else {
            $mail->line(__('forms.confirmation_email_default_body', ['form' => $formTitle]));
        }

        $mail->line('---');
        $mail->line('**' . __('forms.submission_details') . ':**');

        $data = $this->submission->data ?? [];
        $fields = $form?->fields ?? [];

        foreach ($data as $key => $val) {
            $fieldDef = collect($fields)->firstWhere('key', $key);
            $label = $key;
            if ($fieldDef && isset($fieldDef['label'])) {
                $label = is_array($fieldDef['label'])
                    ? ($fieldDef['label'][$locale] ?? $fieldDef['label']['sr'] ?? $fieldDef['label']['en'] ?? $key)
                    : (string) $fieldDef['label'];
            }

            if (is_array($val)) {
                $val = implode(', ', $val);
            }

            $mail->line("**{$label}**: " . ($val ?: '-'));
        }

        return $mail;
    }
}
