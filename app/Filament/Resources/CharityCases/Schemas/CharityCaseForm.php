<?php

namespace App\Filament\Resources\CharityCases\Schemas;

use App\Enums\CasePriority;
use App\Enums\CaseStatus;
use App\Enums\VisitStatusCase;
use App\Filament\Resources\Families\RelationManagers\CharityCasesRelationManager;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CharityCaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Case Information'))
                    ->columns(3)
                    ->schema([
                        Select::make('family_id')
                            ->disabledOn(CharityCasesRelationManager::class)
                            ->label(__('Family'))
                            ->relationship('family', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('family_member_id')
                            ->label(__('Family Member'))
                            ->relationship('familyMember', 'name', fn ($query, Get $get) => $query->where('family_id', $get('family_id')))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('case_type_id')
                            ->label(__('Case Type'))
                            ->relationship('caseType', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
                Section::make(__('Classification'))
                    ->columns(4)
                    ->schema([
                        Select::make('priority')
                            ->label(__('Priority'))
                            ->options(CasePriority::class)
                            ->searchable()
                            ->preload()
                            ->required(),
                        ToggleButtons::make('status')
                            ->label(__('Status'))
                            ->columnSpan(2)
                            ->options(CaseStatus::class)
                            ->inline()
                            ->required(),
                        ToggleButtons::make('visit_status')
                            ->label(__('Visit Status'))
                            ->options(VisitStatusCase::class)
                            ->inline()
                            ->required(),
                    ]),
                Section::make(__('Service Details'))
                    ->schema([
                        Textarea::make('description')
                            ->hiddenLabel()
                            ->label(__('Service Details'))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make(__('Amounts'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('requested_amount')
                            ->label(__('Requested Amount'))
                            ->required()
                            ->numeric()
                            ->currency()
                            ->default(0.0),
                        TextInput::make('approved_amount')
                            ->label(__('Approved Amount'))
                            ->required()
                            ->numeric()
                            ->currency()
                            ->default(0.0),
                    ]),
                Section::make(__('Timeline'))
                    ->columns(2)
                    ->hiddenOn('create')
                    ->schema([
                        TextEntry::make('registered_at')
                            ->dateTime()->placeholder('-')
                            ->label(__('Registered At')),
                        TextEntry::make('reviewed_at')
                            ->dateTime()->placeholder('-')
                            ->label(__('Reviewed At')),
                        TextEntry::make('approved_at')
                            ->dateTime()->placeholder('-')
                            ->label(__('Approved At')),
                        TextEntry::make('closed_at')
                            ->dateTime()->placeholder('-')
                            ->label(__('Closed At')),
                        TextEntry::make('last_visit_at')
                            ->dateTime()->placeholder('-')
                            ->label(__('Last Visit At')),
                        TextEntry::make('next_visit_at')
                            ->label(__('Next Visit At'))
                            ->dateTime()->placeholder('-'),
                    ]),
                Section::make(__('Notes'))
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('Notes'))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
