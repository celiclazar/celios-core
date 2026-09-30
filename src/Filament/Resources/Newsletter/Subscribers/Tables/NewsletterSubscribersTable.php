<?php

namespace Celios\Core\Filament\Resources\Newsletter\Subscribers\Tables;

use Celios\Core\Jobs\Newsletter\SendSubscriptionConfirmationJob;
use Celios\Core\Models\NewsletterSubscriber;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NewsletterSubscribersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->label(__('fields.email'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('full_name')
                    ->label(__('fields.name'))
                    ->searchable(['first_name', 'last_name']),

                TextColumn::make('locale')
                    ->label(__('fields.language'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->color('info'),

                TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'bounced' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => __('newsletter.status_pending'),
                        'active' => __('newsletter.status_active'),
                        'unsubscribed' => __('newsletter.status_unsubscribed'),
                        'bounced' => __('newsletter.status_bounced'),
                        default => $state,
                    }),

                TextColumn::make('signup_source')
                    ->label('Source')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('verified_at')
                    ->label('Verified')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options([
                        'active' => __('newsletter.status_active'),
                        'pending' => __('newsletter.status_pending'),
                        'unsubscribed' => __('newsletter.status_unsubscribed'),
                        'bounced' => __('newsletter.status_bounced'),
                    ]),

                SelectFilter::make('locale')
                    ->label(__('fields.language'))
                    ->options(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano'])),
            ])
            ->recordActions([
                Action::make('resendVerification')
                    ->label('Resend Confirmation')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn (NewsletterSubscriber $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (NewsletterSubscriber $record) {
                        SendSubscriptionConfirmationJob::dispatch($record);
                        Notification::make()
                            ->title('Verification email dispatched')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
