<?php

namespace App\Filament\Actions;

use App\Enums\DeliveryStatus;
use App\Enums\ScheduleStatus;
use App\Models\AssistanceDelivery;
use App\Models\AssistanceSchedule;
use App\Models\Supplier;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;

class CreateDeliveryAction
{
    public static function make(): Action
    {
        return Action::make('createDelivery')
            ->label(__('Create Delivery'))
            ->icon('heroicon-o-truck')
            ->button()
            ->slideOver()
            ->modalWidth(Width::ScreenExtraLarge)
            ->visible(fn (AssistanceSchedule $record): bool => in_array($record->status, [ScheduleStatus::Scheduled, ScheduleStatus::Approved]))
            ->schema([
                Section::make(__('Delivery Info'))
                    ->columns(2)
                    ->schema([
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
                            ->nullable()
                            ->searchable()
                            ->options(fn (AssistanceSchedule $record): array => Supplier::query()
                                ->whereHas('assistanceTypes', fn ($q) => $q->where('assistance_types.id', $record->assistance_type_id))
                                ->pluck('name', 'id')
                                ->all()),
                    ]),
                Section::make(__('Receiver'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('received_by_name')
                            ->label(__('Received By Name')),
                        TextInput::make('received_by_phone')
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
            ])
            ->action(function (array $data, AssistanceSchedule $record): void {
                AssistanceDelivery::query()->create([
                    ...$data,
                    'assistance_schedule_id' => $record->getKey(),
                ]);

                Notification::make()
                    ->title(__('Delivery created successfully'))
                    ->success()
                    ->send();
            });
    }
}
