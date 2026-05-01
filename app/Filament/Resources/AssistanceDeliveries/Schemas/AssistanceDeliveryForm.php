<?php

namespace App\Filament\Resources\AssistanceDeliveries\Schemas;

use App\Enums\DeliveryStatus;
use App\Filament\Resources\AssistanceSchedules\RelationManagers\AssistanceDeliveriesRelationManager;
use App\Models\AssistanceSchedule;
use App\Models\Supplier;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class AssistanceDeliveryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Delivery Info'))
                    ->columns(2)
                    ->schema([
                        Select::make('assistance_schedule_id')
                            ->hiddenOn(AssistanceDeliveriesRelationManager::class)
                            ->label(__('Assistance Schedule'))
                            ->relationship('assistanceSchedule', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->charityCase?->title ? "{$record->charityCase->title} (#{$record->id})" : (string) $record->id)
                            ->searchable()
                            ->preload()
                            ->required(),
                        DateTimePicker::make('delivered_at')
                            ->label(__('Delivered At')),
                        Select::make('delivery_status')
                            ->label(__('Delivery Status'))
                            ->options(DeliveryStatus::class)
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('supplier_id')
                            ->label(__('Supplier'))
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->options(function (Get $get, ?object $livewire = null): array {
                                $scheduleId = $get('assistance_schedule_id')
                                    ?? ($livewire instanceof AssistanceDeliveriesRelationManager
                                        ? $livewire->getOwnerRecord()->getKey()
                                        : null);

                                if (! $scheduleId) {
                                    return Supplier::query()->pluck('name', 'id')->all();
                                }

                                $typeId = AssistanceSchedule::find($scheduleId)?->assistance_type_id;

                                if (! $typeId) {
                                    return Supplier::query()->pluck('name', 'id')->all();
                                }

                                return Supplier::query()
                                    ->whereHas('assistanceTypes', fn ($q) => $q->where('assistance_types.id', $typeId))
                                    ->pluck('name', 'id')
                                    ->all();
                            })
                            ->nullable(),
                    ]),
                Section::make(__('Receiver'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('received_by_name')
                            ->label(__('Received By Name')),
                        PhoneInput::make('received_by_phone')
                            ->label(__('Received By Phone')),
                    ]),
                Section::make(__('Proof'))
                    ->schema([
                        FileUpload::make('proof_file_path')
                            ->label(__('Proof'))
                            ->visibility('public')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make(__('Communication & Notes'))
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('Communication Notes'))
                            ->helperText(__('Use this field for delivery coordination, case contact notes, and any delivery remarks.'))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
