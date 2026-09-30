<?php

namespace Celios\Core\Filament\Resources\Pages\RelationManagers;

use Celios\Core\Models\Page;
use Celios\Core\Models\PageRevision;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RevisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'revisions';

    public static function getTitle(mixed $ownerRecord, string $pageClass): string
    {
        return __('sidebar.revisions') ?? 'Page Revisions & History';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('created_at')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label(fn () => __('fields.revision_date'))
                    ->dateTime('d.m.Y H:i:s')
                    ->description(fn (PageRevision $record): string => $record->created_at->diffForHumans())
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label(fn () => __('fields.author'))
                    ->default(fn () => __('fields.system_user'))
                    ->icon('heroicon-o-user'),

                TextColumn::make('blocks_count')
                    ->label(fn () => __('fields.blocks_count'))
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (int $state): string => $state . ' ' . __('fields.blocks_unit')),

                TextColumn::make('block_types')
                    ->label(fn () => __('fields.block_types'))
                    ->badge()
                    ->color('gray')
                    ->separator(',')
                    ->limitList(3)
                    ->expandableLimitedList(),

                TextColumn::make('note')
                    ->label(fn () => __('fields.revision_note'))
                    ->placeholder('—')
                    ->limit(40),

                IconColumn::make('is_current')
                    ->label(fn () => __('fields.current_live_version'))
                    ->boolean()
                    ->state(fn (PageRevision $record): bool => $record->isCurrentLive())
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->headerActions([
                Action::make('create_snapshot')
                    ->label(fn () => __('actions.save_snapshot'))
                    ->icon('heroicon-o-camera')
                    ->color('gray')
                    ->form([
                        TextInput::make('note')
                            ->label(fn () => __('fields.snapshot_note'))
                            ->placeholder(fn () => __('fields.snapshot_note_placeholder'))
                            ->maxLength(255),
                    ])
                    ->action(function (array $data): void {
                        /** @var Page $page */
                        $page = $this->getOwnerRecord();
                        $page->createRevision(
                            userId: auth()->id(),
                            note: ! empty($data['note']) ? $data['note'] : __('notifications.manual_snapshot')
                        );

                        Notification::make()
                            ->title(fn () => __('notifications.snapshot_created'))
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label(fn () => __('actions.preview'))
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->openUrlInNewTab()
                    ->url(function (PageRevision $record): string {
                        /** @var Page $page */
                        $page = $this->getOwnerRecord();
                        $locale = app()->getLocale();
                        $slug = $page->getTranslation('slug', $locale, true) ?: 'home';

                        return url("/{$locale}/{$slug}?preview=true&revision_id={$record->id}");
                    }),

                Action::make('restore')
                    ->label(fn () => __('actions.restore_to_draft'))
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(fn () => __('actions.restore_to_draft_heading'))
                    ->modalDescription(fn () => __('actions.restore_to_draft_description'))
                    ->action(function (PageRevision $record): void {
                        /** @var Page $page */
                        $page = $this->getOwnerRecord();
                        $page->restoreRevision($record);

                        Notification::make()
                            ->title(fn () => __('notifications.revision_restored_to_draft'))
                            ->success()
                            ->send();

                        $this->dispatch('refreshDraftContent');
                    }),

                DeleteAction::make()
                    ->label(fn () => __('actions.delete')),
            ]);
    }
}
