<?php

namespace App\Filament\Resources\Visits\Schemas;

use App\Enums\VisitStatus;
use App\Enums\VisitType;
use App\Filament\Resources\CharityCases\Schemas\CharityCaseSelect;
use App\Filament\Resources\Families\RelationManagers\VisitsRelationManager;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class VisitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Visit Details'))
                    ->columns(2)
                    ->schema([
                        CharityCaseSelect::make()
                            ->relationship(
                                'charityCase',
                                'code',
                                fn ($query, $livewire = null) => $query
                                    ->when(
                                        $livewire && $livewire instanceof VisitsRelationManager,
                                        fn ($query) => $query->where('charity_cases.family_id', $livewire->getOwnerRecord()->getKey())
                                    )
                                    ->join('family_members', 'family_members.id', '=', 'charity_cases.family_member_id')
                                    ->select('charity_cases.*', 'family_members.name as family_member_name')
                            ),
                        Select::make('visit_type')
                            ->label(__('Visit Type'))
                            ->options(VisitType::class)
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('status')
                            ->label(__('Status'))
                            ->options(VisitStatus::class)
                            ->searchable()
                            ->lazy()
                            ->preload()
                            ->required(),
                        DatePicker::make('scheduled_at')
                            ->visible(fn (Get $get) => $get('status') === VisitStatus::Scheduled)
                            ->label(__('Scheduled At')),
                        DatePicker::make('visited_at')
                            ->label(__('Visited At')),
                        DatePicker::make('next_visit_at')
                            ->label(__('Next Visit At')),
                    ]),
                Section::make(__('Findings'))
                    ->schema([
                        Textarea::make('summary')
                            ->label(__('Summary'))
                            ->columnSpanFull(),
                        Textarea::make('findings')
                            ->label(__('Findings'))
                            ->columnSpanFull(),
                        Textarea::make('recommendations')
                            ->label(__('Recommendations'))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make(__('Notes'))
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('Notes'))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make(__('Incomes'))
                    ->schema([
                        Repeater::make('visitIncomes')
                            ->relationship()
                            ->label(__('Incomes'))
                            ->schema([
                                TextInput::make('description')
                                    ->label(__('Description'))
                                    ->required()
                                    ->columnSpan(2),
                                TextInput::make('amount')
                                    ->label(__('Amount'))
                                    ->numeric()
                                    ->required()
                                    ->prefix('EGP')
                                    ->columnSpan(1),
                                Textarea::make('notes')
                                    ->label(__('Notes'))
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->live()
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make(__('Expenses'))
                    ->schema([
                        Repeater::make('visitExpenses')
                            ->relationship()
                            ->label(__('Expenses'))
                            ->schema([
                                TextInput::make('description')
                                    ->label(__('Description'))
                                    ->required()
                                    ->columnSpan(2),
                                TextInput::make('amount')
                                    ->label(__('Amount'))
                                    ->numeric()
                                    ->required()
                                    ->prefix('EGP')
                                    ->columnSpan(1),
                                Textarea::make('notes')
                                    ->label(__('Notes'))
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->live()
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make(__('Net Total'))
                    ->schema([
                        Placeholder::make('net_amount')
                            ->label(__('Net Total'))
                            ->content(function (Get $get): string {
                                $income = collect($get('visitIncomes') ?? [])->sum('amount');
                                $expense = collect($get('visitExpenses') ?? [])->sum('amount');

                                return number_format((float) $income - (float) $expense, 2).' EGP';
                            }),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
