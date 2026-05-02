<?php

namespace App\Filament\Resources\AssistanceSchedules\Pages;

use App\Filament\Actions\AddNoteAction;
use App\Filament\Actions\CreateDeliveryAction;
use App\Filament\Resources\AssistanceSchedules\AssistanceScheduleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAssistanceSchedule extends ViewRecord
{
    protected static string $resource = AssistanceScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateDeliveryAction::make(),
            AddNoteAction::make(),
            EditAction::make(),
        ];
    }
}
