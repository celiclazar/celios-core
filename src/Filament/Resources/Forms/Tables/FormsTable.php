<?php

namespace Celios\Core\Filament\Resources\Forms\Tables;

use Celios\Core\Filament\Resources\Forms\FormSubmissionResource;
use Celios\Core\Models\Form;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FormsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('forms.form_title'))
                    ->state(fn (Form $record): string => $record->getTranslation('title', app()->getLocale(), true) ?: __('fields.no_title'))
                    ->searchable(),

                TextColumn::make('slug')
                    ->label(__('forms.form_slug'))
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('fields_count')
                    ->label(__('forms.form_fields'))
                    ->state(fn (Form $record): int => is_array($record->fields) ? count($record->fields) : 0)
                    ->badge()
                    ->color('info'),

                TextColumn::make('submissions_count')
                    ->label(__('forms.submissions_count'))
                    ->counts('submissions')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('forms.is_active'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                Action::make('view_submissions')
                    ->label(__('forms.view_submissions'))
                    ->icon('heroicon-o-inbox-stack')
                    ->color('primary')
                    ->url(fn (Form $record): string => FormSubmissionResource::getUrl('index', [
                        'tableFilters' => [
                            'form_id' => [
                                'value' => $record->id,
                            ],
                        ],
                    ])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
