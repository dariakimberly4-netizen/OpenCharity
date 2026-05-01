<?php

namespace App\Filament\Resources\AssistanceSchedules\Tables;

use App\Enums\FundingStatus;
use App\Enums\ScheduleFrequency;
use App\Enums\ScheduleStatus;
use App\Filament\Actions\AddNoteAction;
use App\Filament\Actions\CreateDeliveryAction;
use App\Filament\Exports\AssistanceScheduleExporter;
use App\Models\AssistanceSchedule;
use App\Models\AssistanceType;
use App\Models\CharityCase;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter as DaterangepickerFilter;
use Tapp\FilamentValueRangeFilter\Filters\ValueRangeFilter;

class AssistanceSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('assistance_schedules.status', '!=', ScheduleStatus::Canceled))
            ->columns([
                IconColumn::make('is_parent')
                    ->label(__('Series'))
                    ->boolean()
                    ->state(fn (AssistanceSchedule $record): bool => $record->isParent()),
                TextColumn::make('series_label')
                    ->label(__('Occurrence'))
                    ->badge()
                    ->state(function (AssistanceSchedule $record): string {
                        if ($record->isParent()) {
                            return __('Series');
                        }

                        if ($record->isChild() && $record->occurrence_number) {
                            return __('Occurrence #:number', ['number' => $record->occurrence_number]);
                        }

                        return __('Single');
                    }),
                TextColumn::make('charityCase.full_identifier')
                    ->badge()
                    ->limit(50)
                    ->label(__('Charity Case'))
                    ->searchable(),
                TextColumn::make('charityCase.code')
                    ->label(__('Case Code'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('assistanceType.name')
                    ->label(__('Assistance Type'))
                    ->searchable(),
                TextColumn::make('scheduled_date')
                    ->label(__('Scheduled Date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('scheduled_time')
                    ->label(__('Scheduled Time'))
                    ->time()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->currency()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label(__('Quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('frequency')
                    ->label(__('Frequency'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('funding_status')
                    ->label(__('Funding Status'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(__('Deleted At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('charity_case_id')
                    ->label(__('Charity Case'))
                    ->options(fn (): array => CharityCase::query()->pluck('code', 'id')->all())
                    ->searchable(),
                SelectFilter::make('assistance_type_id')
                    ->label(__('Assistance Type'))
                    ->options(fn (): array => AssistanceType::query()->pluck('name', 'id')->all())
                    ->searchable(),
                SelectFilter::make('frequency')
                    ->label(__('Frequency'))
                    ->options(ScheduleFrequency::class)
                    ->searchable(),
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(ScheduleStatus::class)
                    ->searchable(),
                SelectFilter::make('funding_status')
                    ->label(__('Funding Status'))
                    ->options(FundingStatus::class)
                    ->searchable(),
                DaterangepickerFilter::make('scheduled_date')
                    ->label(__('Scheduled Date'))
                    ->useColumn('scheduled_date'),

                ValueRangeFilter::make('amount_range')
                    ->label(__('Amount Range')),
                ValueRangeFilter::make('quantity_range')
                    ->label(__('Quantity Range')),
                Filter::make('series_type')
                    ->label(__('Series Type'))
                    ->schema([
                        Select::make('value')
                            ->label(__('Series Type'))
                            ->options([
                                'parent' => __('Series'),
                                'child' => __('Occurrence'),
                                'single' => __('Single'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'parent' => $query->whereNull('parent_schedule_id')->where('frequency', '!=', ScheduleFrequency::Once->value),
                            'child' => $query->whereNotNull('parent_schedule_id'),
                            'single' => $query->whereNull('parent_schedule_id')->where('frequency', ScheduleFrequency::Once),
                            default => $query,
                        };
                    }),
                TernaryFilter::make('has_deliveries')
                    ->label(__('Has Deliveries'))
                    ->queries(
                        true: fn (Builder $query): Builder => $query->has('assistanceDeliveries'),
                        false: fn (Builder $query): Builder => $query->doesntHave('assistanceDeliveries'),
                        blank: fn (Builder $query): Builder => $query,
                    ),
                Filter::make('parents_only')
                    ->label(__('Parents only'))
                    ->query(fn (Builder $query): Builder => $query->whereNull('parent_schedule_id')),
                TrashedFilter::make(),
            ])
            ->recordActions([
                CreateDeliveryAction::make(),
                AddNoteAction::make(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__('Export'))
                    ->exporter(AssistanceScheduleExporter::class),
            ]);
    }
}
