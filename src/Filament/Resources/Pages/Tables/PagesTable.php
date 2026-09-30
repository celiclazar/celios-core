<?php

namespace Celios\Core\Filament\Resources\Pages\Tables;

use Celios\Core\Enums\PageType;
use Celios\Core\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(fn () => __('fields.title'))
                    ->state(fn (Page $record): string => (
                    $record->getTranslation('title', app()->getLocale(), true)
                        ?: __('fields.no_title')
                    ))
                    ->searchable(),

                TextColumn::make('slug')
                    ->label(fn () => __('fields.url_slug'))
                    ->state(fn (Page $record): string => (
                    $record->getTranslation('slug', app()->getLocale(), true)
                        ?: ''
                    ))
                    ->searchable(),

                TextColumn::make('type')
                    ->label(fn () => __('fields.page_type'))
                    ->badge()
                    ->state(fn (Page $record): string => $record->type instanceof PageType ? $record->type->label() : ($record->type ?? 'Standard'))
                    ->color(fn (Page $record): string => $record->type instanceof PageType ? $record->type->badgeColor() : 'gray')
                    ->sortable(),

                IconColumn::make('has_changes')
                    ->label(fn () => __('fields.has_changes'))
                    ->boolean()
                    ->state(fn (Page $record): bool => ! empty($record->draft_content))
                    ->trueIcon('heroicon-o-document-text')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('warning')
                    ->falseColor('success'),

                IconColumn::make('is_visible')
                    ->label(fn () => __('fields.visible_on_site'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(fn () => __('fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(fn () => __('fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(fn () => __('fields.page_type'))
                    ->options(collect(PageType::cases())->mapWithKeys(fn (PageType $type) => [
                        $type->value => $type->label(),
                    ])),
            ])
            ->recordActions([
                Action::make('visual_builder')
                    ->label('Visual')
                    ->icon('heroicon-o-paint-brush')
                    ->color('primary')
                    ->url(fn (Page $record): string => route('admin.visual_builder.edit', ['page' => $record->id])),

                EditAction::make()
                    ->label(fn () => __('actions.edit')),

                \Filament\Actions\ActionGroup::make([
                    Action::make('preview')
                        ->label(fn () => __('actions.preview'))
                        ->icon('heroicon-o-eye')
                        ->color('gray')
                        ->openUrlInNewTab()
                        ->url(function (Page $record): string {
                            $locale = app()->getLocale();
                            $slug = $record->getTranslation('slug', $locale, true);

                            return url("/{$locale}/{$slug}?preview=true");
                        }),

                    Action::make('publish')
                        ->label(fn () => __('actions.publish'))
                        ->icon('heroicon-m-cloud-arrow-up')
                        ->color('success')
                        ->requiresConfirmation()
                        ->hidden(fn (Page $record): bool => empty($record->draft_content))
                        ->action(function (Page $record): void {
                            $record->publish(auth()->id());

                            Notification::make()
                                ->title(fn () => __('notifications.page_launched'))
                                ->success()
                                ->send();
                        }),

                    DeleteAction::make()
                        ->label(fn () => __('actions.delete'))
                        ->hidden(fn (Page $record): bool => $record->isSystem()),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(fn () => __('actions.delete_selected'))
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->reject(fn (Page $page) => $page->isSystem())->each->delete();
                        }),
                ])->label(fn () => __('actions.bulk_actions')),
            ]);
    }
}
