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

    public function __construct(public FormSubmission $submission) {}

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

        $data = $this->submission->data ?? [];
        $fields = $form?->fields ?? [];

        $subject = filled($customSubject)
            ? str_replace('•', '-', $customSubject)
            : __('forms.confirmation_email_default_subject', ['form' => $formTitle]);

        $clientName = $data['name'] ?? null;
        $greeting = filled($clientName)
            ? "Hi {$clientName},"
            : __('forms.confirmation_email_default_greeting');

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting);

        // Set Reply-To to the form recipient so clients can reply directly
        $replyToEmail = $form?->recipient_emails[0] ?? config('mail.from.address');
        if ($replyToEmail && filter_var($replyToEmail, FILTER_VALIDATE_EMAIL)) {
            $mail->replyTo($replyToEmail, config('app.name'));
        }

        if (filled($customBody)) {
            $cleanBody = trim(strip_tags($customBody, '<strong><em><b><i><a><br>'));
            $mail->line(new \Illuminate\Support\HtmlString($cleanBody));
        } else {
            $mail->line(__('forms.confirmation_email_default_body', ['form' => $formTitle]));
        }

        $mail->line('**'.__('forms.submission_details').':**');

        foreach ($data as $key => $val) {
            if (in_array($key, ['email', 'phone'], true) || empty($val)) {
                continue;
            }

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

            $mail->line("**{$label}**: ".\Illuminate\Support\Str::limit((string) $val, 120));
        }

        return $mail;
    }
}
