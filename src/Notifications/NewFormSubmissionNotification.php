<?php

namespace Celios\Core\Notifications;

use Celios\Core\Models\FormSubmission;
use Filament\Actions\Action as NotificationAction;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewFormSubmissionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public FormSubmission $submission)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $form = $this->submission->form;
        $locale = app()->getLocale();
        $formTitle = $form ? ($form->getTranslation('title', $locale, true) ?: $form->slug) : 'Form';

        $mail = (new MailMessage)
            ->subject(__('forms.submission_single') . ': ' . $formTitle)
            ->greeting(__('forms.submission_details'))
            ->line(__('forms.form_title') . ': ' . $formTitle);

        $data = $this->submission->data ?? [];
        $fields = $form?->fields ?? [];

        foreach ($data as $key => $val) {
            $fieldDef = collect($fields)->firstWhere('key', $key);
            $label = $key;
            if ($fieldDef && isset($fieldDef['label'])) {
                $label = is_array($fieldDef['label'])
                    ? ($fieldDef['label'][$locale] ?? $fieldDef['label']['sr'] ?? $fieldDef['label']['en'] ?? $key)
                    : (string)$fieldDef['label'];
            }

            if (is_array($val)) {
                $val = implode(', ', $val);
            }

            $mail->line("**{$label}**: " . ($val ?: '-'));
        }

        if (!empty($this->submission->files)) {
            $mail->line(__('forms.files_attached') . ': ' . count($this->submission->files));
        }

        try {
            $viewUrl = \Celios\Core\Filament\Resources\Forms\FormSubmissionResource::getUrl('view', ['record' => $this->submission->id]);
            $mail->action(__('forms.view_submissions'), $viewUrl);
        } catch (\Throwable $e) {
            // If route generation fails in CLI/cron
        }

        return $mail;
    }

    public function toDatabase(object $notifiable): array
    {
        $form = $this->submission->form;
        $locale = app()->getLocale();
        $formTitle = $form ? ($form->getTranslation('title', $locale, true) ?: $form->slug) : 'Form';

        try {
            $viewUrl = \Celios\Core\Filament\Resources\Forms\FormSubmissionResource::getUrl('view', ['record' => $this->submission->id]);
        } catch (\Throwable $e) {
            $viewUrl = url('/admin/forms/form-submissions/' . $this->submission->id);
        }

        return FilamentNotification::make()
            ->title(__('forms.submission_single') . ': ' . $formTitle)
            ->body(__('forms.submitted_at') . ': ' . now()->format('d.m.Y H:i'))
            ->icon('heroicon-o-inbox-stack')
            ->iconColor('success')
            ->actions([
                NotificationAction::make('view')
                    ->label(__('forms.view_submissions'))
                    ->url($viewUrl),
            ])
            ->getDatabaseMessage();
    }
}
