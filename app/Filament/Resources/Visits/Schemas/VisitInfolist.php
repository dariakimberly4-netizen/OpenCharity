<?php

namespace App\Filament\Resources\Visits\Schemas;

use App\Models\Visit;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VisitInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Visit Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('charityCase.code')
                            ->label(__('Charity Case'))
                            ->badge(),
                        TextEntry::make('charityCase.family.code')
                            ->label(__('Family'))
                            ->badge(),
                        TextEntry::make('visit_type')
                            ->label(__('Visit Type'))
                            ->badge(),
                        TextEntry::make('status')
                            ->label(__('Status'))
                            ->badge(),
                        TextEntry::make('scheduled_at')
                            ->label(__('Scheduled At'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('visited_at')
                            ->label(__('Visited At'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('next_visit_at')
                            ->label(__('Next Visit At'))
                            ->date()
                            ->placeholder('-'),
                    ]),
                Section::make(__('Findings'))
                    ->schema([
                        TextEntry::make('summary')
                            ->label(__('Summary'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('findings')
                            ->label(__('Findings'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('recommendations')
                            ->label(__('Recommendations'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
                Section::make(__('Notes'))
                    ->schema([
                        TextEntry::make('notes')
                            ->label(__('Notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
                Section::make(__('Incomes'))
                    ->schema([
                        RepeatableEntry::make('visitIncomes')
                            ->label(__('Incomes'))
                            ->schema([
                                TextEntry::make('description')->label(__('Description')),
                                TextEntry::make('amount')->label(__('Amount'))->currency(),
                                TextEntry::make('notes')->label(__('Notes'))->placeholder('-'),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make(__('Expenses'))
                    ->schema([
                        RepeatableEntry::make('visitExpenses')
                            ->label(__('Expenses'))
                            ->schema([
                                TextEntry::make('description')->label(__('Description')),
                                TextEntry::make('amount')->label(__('Amount'))->currency(),
                                TextEntry::make('notes')->label(__('Notes'))->placeholder('-'),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make(__('Net Total'))
                    ->schema([
                        TextEntry::make('net_amount')
                            ->label(__('Net Total'))
                            ->state(fn (Visit $record): string => number_format((float) $record->net_amount, 2).' EGP'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
