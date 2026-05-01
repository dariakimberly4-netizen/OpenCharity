<?php

namespace App\Filament\Actions;

use App\Models\AssistanceSchedule;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;

class AddNoteAction
{
    public static function make(): Action
    {
        return Action::make('addNote')
            ->label(__('Add Note'))
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->slideOver()
            ->schema([
                Section::make(__('Note'))
                    ->schema([
                        Textarea::make('note')
                            ->label(__('Note'))
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])
            ->action(function (array $data, AssistanceSchedule $record): void {
                $record->addNote($data['note'], auth()->user());

                Notification::make()
                    ->title(__('Note added successfully'))
                    ->success()
                    ->send();
            });
    }
}
