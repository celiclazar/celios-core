<?php

namespace Celios\Core\Filament\Resources\Activities\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(fn () => __('fields.log_metadata'))
                    ->description(fn () => __('fields.log_metadata_desc'))
                    ->aside()
                    ->schema([
                        TextEntry::make('description')
                            ->label(fn () => __('fields.activity_action'))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'created' => 'success',
                                'updated' => 'warning',
                                'deleted' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state) => match ($state) {
                                'created' => __('fields.action_created'),
                                'updated' => __('fields.action_updated'),
                                'deleted' => __('fields.action_deleted'),
                                default => str($state)->ucfirst(),
                            }),

                        TextEntry::make('causer.name')
                            ->label(fn () => __('fields.user_responsible'))
                            ->default(fn () => __('fields.system_automated')),

                        TextEntry::make('subject_type')
                            ->label(fn () => __('fields.target_module'))
                            ->formatStateUsing(fn ($state) => str($state)->afterLast('\\')),

                        TextEntry::make('created_at')
                            ->label(fn () => __('fields.activity_timestamp'))
                            ->dateTime('M j, Y — H:i:s'),
                    ]),

                Section::make(fn () => __('fields.data_audit'))
                    ->description(fn () => __('fields.data_audit_desc'))
                    ->schema([
                        KeyValueEntry::make('attribute_changes.old')
                            ->label(fn () => __('fields.original_values'))
                            ->keyLabel(fn () => __('fields.kv_field'))
                            ->valueLabel(fn () => __('fields.kv_previous'))
                            ->placeholder(fn () => __('fields.no_previous_data')),

                        KeyValueEntry::make('attribute_changes.attributes')
                            ->label(fn () => __('fields.updated_values'))
                            ->keyLabel(fn () => __('fields.kv_field'))
                            ->valueLabel(fn () => __('fields.kv_new_value')),
                    ])->columns(2),
            ]);
    }
}
