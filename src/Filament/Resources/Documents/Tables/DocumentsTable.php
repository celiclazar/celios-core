<?php

namespace Celios\Core\Filament\Resources\Documents\Tables;

use Celios\Core\Enums\DocumentAccessLevel;
use Celios\Core\Models\Document;
use Celios\Core\Models\DocumentCategory;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('fields.title'))
                    ->state(fn (Document $record): string => $record->getLocalizedTitle())
                    ->description(fn (Document $record): ?string => $record->file_name)
                    ->searchable(),

                TextColumn::make('category.title')
                    ->label(__('documents.category'))
                    ->state(fn (Document $record): string => $record->category
                        ? $record->category->getLocalizedTitle()
                        : '-'
                    )
                    ->badge()
                    ->color(fn (Document $record): string => ($record->category?->is_internal) ? 'danger' : 'info')
                    ->searchable(),

                TextColumn::make('file_type')
                    ->label(__('documents.file_type'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => strtoupper($state ?? 'FILE'))
                    ->color(fn (?string $state): string => match (strtolower($state ?? '')) {
                        'pdf' => 'danger',
                        'doc', 'docx' => 'info',
                        'xls', 'xlsx', 'csv' => 'success',
                        'zip', 'rar', 'tar', 'gz' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('formatted_size')
                    ->label(__('documents.file_size'))
                    ->state(fn (Document $record): string => $record->formatted_size)
                    ->toggleable(),

                TextColumn::make('access_level')
                    ->label(__('documents.access_level'))
                    ->badge()
                    ->formatStateUsing(fn (DocumentAccessLevel $state): string => $state->label())
                    ->color(fn (DocumentAccessLevel $state): string => $state->color())
                    ->icon(fn (DocumentAccessLevel $state): string => $state->icon()),

                TextColumn::make('version')
                    ->label(__('documents.version'))
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('downloads_count')
                    ->label(__('documents.downloads'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                IconColumn::make('is_published')
                    ->label(__('fields.is_published'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('category_id')
                    ->label(__('documents.category'))
                    ->options(function (): array {
                        return DocumentCategory::query()
                            ->get()
                            ->mapWithKeys(fn (DocumentCategory $cat) => [
                                $cat->id => $cat->getLocalizedTitle(),
                            ])
                            ->all();
                    }),

                SelectFilter::make('access_level')
                    ->label(__('documents.access_level'))
                    ->options(collect(DocumentAccessLevel::cases())->mapWithKeys(fn (DocumentAccessLevel $lvl) => [
                        $lvl->value => $lvl->label(),
                    ])),
            ])
            ->recordActions([
                Action::make('download')
                    ->label(__('documents.download'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (Document $record): string => route('documents.admin.download', $record->id))
                    ->openUrlInNewTab(),

                Action::make('preview')
                    ->label(__('documents.preview'))
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Document $record): string => route('documents.admin.preview', $record->id))
                    ->openUrlInNewTab(),

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
