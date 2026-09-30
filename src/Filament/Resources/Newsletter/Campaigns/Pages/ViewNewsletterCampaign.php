<?php

namespace Celios\Core\Filament\Resources\Newsletter\Campaigns\Pages;

use Celios\Core\Filament\Resources\Newsletter\Campaigns\NewsletterCampaignResource;
use Celios\Core\Jobs\Newsletter\SendNewsletterCampaignJob;
use Celios\Core\Mail\Newsletter\NewsletterCampaignMail;
use Celios\Core\Models\NewsletterCampaign;
use Celios\Core\Models\NewsletterSubscriber;
use Celios\Core\Services\Newsletter\NewsletterMailerService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewNewsletterCampaign extends ViewRecord
{
    protected static string $resource = NewsletterCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendCampaign')
                ->label('Send Now')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->visible(fn (NewsletterCampaign $record): bool => in_array($record->status, ['draft', 'scheduled']))
                ->requiresConfirmation()
                ->modalHeading('Send Newsletter Campaign')
                ->modalDescription('Are you sure you want to dispatch this campaign to all eligible subscribers?')
                ->action(function (NewsletterCampaign $record) {
                    SendNewsletterCampaignJob::dispatch($record);

                    Notification::make()
                        ->title('Campaign dispatch started!')
                        ->body('Emails are being sent in the background according to your throttling configuration.')
                        ->success()
                        ->send();
                }),

            Action::make('sendTest')
                ->label('Test Send')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->form([
                    TextInput::make('test_email')
                        ->label('Recipient Email')
                        ->email()
                        ->default(fn () => auth()->user()?->email)
                        ->required(),
                ])
                ->action(function (NewsletterCampaign $record, array $data, NewsletterMailerService $mailerService) {
                    try {
                        $locale = config('locales.default', 'sr');
                        $dummySub = new NewsletterSubscriber([
                            'email' => $data['test_email'],
                            'first_name' => auth()->user()?->name ?? 'Admin',
                            'locale' => $locale,
                            'unsubscribe_token' => 'test-token',
                        ]);

                        $subject = '[TEST] ' . ($record->getTranslation('subject', $locale) ?: $record->title);
                        $content = $record->getTranslation('content', $locale) ?: '';

                        $mailer = $mailerService->getMailer();
                        $mail = new NewsletterCampaignMail(
                            $record,
                            $dummySub,
                            $subject,
                            $content,
                            url('/'),
                            null,
                            $mailerService->getFromAddress(),
                            $mailerService->getFromName()
                        );

                        $mailer->to($data['test_email'])->send($mail);

                        Notification::make()
                            ->title('Test email sent to ' . $data['test_email'])
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Failed sending test: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            EditAction::make()
                ->visible(fn (NewsletterCampaign $record): bool => in_array($record->status, ['draft', 'scheduled'])),

            DeleteAction::make()
                ->visible(fn (NewsletterCampaign $record): bool => in_array($record->status, ['draft', 'scheduled', 'cancelled'])),
        ];
    }
}
