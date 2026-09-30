<?php

namespace Celios\Core\Filament\Resources\Newsletter\Campaigns\Tables;

use Celios\Core\Jobs\Newsletter\SendNewsletterCampaignJob;
use Celios\Core\Mail\Newsletter\NewsletterCampaignMail;
use Celios\Core\Models\NewsletterCampaign;
use Celios\Core\Models\NewsletterSubscriber;
use Celios\Core\Services\Newsletter\NewsletterMailerService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NewsletterCampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Campaign Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sent' => 'success',
                        'sending' => 'info',
                        'scheduled' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => __('newsletter.campaign_status_draft'),
                        'scheduled' => __('newsletter.campaign_status_scheduled'),
                        'sending' => __('newsletter.campaign_status_sending'),
                        'sent' => __('newsletter.campaign_status_sent'),
                        'failed' => __('newsletter.campaign_status_failed'),
                        'cancelled' => __('newsletter.campaign_status_cancelled'),
                        default => $state,
                    }),

                TextColumn::make('recipients_count')
                    ->label('Recipients')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('open_rate')
                    ->label('Open Rate')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (NewsletterCampaign $record): string => $record->open_rate . '% (' . $record->open_count . ')'),

                TextColumn::make('click_rate')
                    ->label('Click Rate')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn (NewsletterCampaign $record): string => $record->click_rate . '% (' . $record->click_count . ')'),

                TextColumn::make('scheduled_at')
                    ->label('Scheduled For')
                    ->dateTime('d.m.Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('sent_at')
                    ->label('Sent At')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime('d.m.Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => __('newsletter.campaign_status_draft'),
                        'scheduled' => __('newsletter.campaign_status_scheduled'),
                        'sending' => __('newsletter.campaign_status_sending'),
                        'sent' => __('newsletter.campaign_status_sent'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('sendTest')
                    ->label('Test Send')
                    ->icon('heroicon-o-paper-airplane')
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

                Action::make('sendCampaign')
                    ->label('Send Now')
                    ->icon('heroicon-o-arrow-up-right')
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

                EditAction::make()
                    ->visible(fn (NewsletterCampaign $record): bool => in_array($record->status, ['draft', 'scheduled'])),

                DeleteAction::make()
                    ->visible(fn (NewsletterCampaign $record): bool => in_array($record->status, ['draft', 'scheduled', 'cancelled'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
