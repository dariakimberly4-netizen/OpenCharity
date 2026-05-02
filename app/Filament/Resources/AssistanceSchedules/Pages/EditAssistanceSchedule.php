<?php

namespace App\Filament\Resources\AssistanceSchedules\Pages;

use App\Filament\Actions\AddNoteAction;
use App\Filament\Actions\CreateDeliveryAction;
use App\Filament\Resources\AssistanceSchedules\AssistanceScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditAssistanceSchedule extends EditRecord
{
    protected static string $resource = AssistanceScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateDeliveryAction::make(),
            AddNoteAction::make(),
        ];
    }
}
