<?php

namespace App\Filament\Resources\FamilyMembers\Tables;

use App\Enums\EducationStatus;
use App\Enums\EmploymentStatus;
use App\Enums\Gender;
use App\Enums\HealthStatus;
use App\Enums\MaritalStatus;
use App\Enums\RelationToHead;
use App\Filament\Actions\PrintFamilyMemberReportAction;
use App\Filament\Exports\FamilyMemberExporter;
use App\Filament\Resources\Families\RelationManagers\FamilyMembersRelationManager;
use App\Models\Family;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter as DaterangepickerFilter;
use Tapp\FilamentValueRangeFilter\Filters\ValueRangeFilter;

class FamilyMembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('Code'))
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('family.name')
                    ->label(__('Family'))
                    ->hiddenOn(FamilyMembersRelationManager::class)
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                TextColumn::make('relation_to_head')
                    ->label(__('Relation To Head'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gender')
                    ->label(__('Gender'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('birth_date')
                    ->label(__('Birth Date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('marital_status')
                    ->label(__('Marital Status'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('education_status')
                    ->label(__('Education Status'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employment_status')
                    ->label(__('Employment Status'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nationality.name')
                    ->label(__('Nationality'))
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('health_status')
                    ->label(__('Health Status'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('diseases.name')
                    ->label(__('Diseases'))
                    ->badge()
                    ->separator(',')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('monthly_income')
                    ->label(__('Monthly Income'))
                    ->currency()
                    ->sortable(),
                TextColumn::make('national_id')
                    ->label(__('National ID'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('family_id')
                    ->label(__('Family'))
                    ->options(fn (): array => Family::query()->pluck('code', 'id')->all())
                    ->searchable(),
                SelectFilter::make('gender')
                    ->label(__('Gender'))
                    ->options(Gender::class)
                    ->searchable(),
                SelectFilter::make('relation_to_head')
                    ->label(__('Relation To Head'))
                    ->options(RelationToHead::class)
                    ->searchable(),
                SelectFilter::make('marital_status')
                    ->label(__('Marital Status'))
                    ->options(MaritalStatus::class)
                    ->searchable(),
                SelectFilter::make('education_status')
                    ->label(__('Education Status'))
                    ->options(EducationStatus::class)
                    ->searchable(),
                SelectFilter::make('employment_status')
                    ->label(__('Employment Status'))
                    ->options(EmploymentStatus::class)
                    ->searchable(),
                SelectFilter::make('health_status')
                    ->label(__('Health Status'))
                    ->options(HealthStatus::class)
                    ->searchable(),
                SelectFilter::make('diseases')
                    ->label(__('Diseases'))
                    ->relationship('diseases', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                SelectFilter::make('nationality_id')
                    ->label(__('Nationality'))
                    ->relationship('nationality', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('has_chronic_disease')
                    ->label(__('Chronic Disease'))
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereHas('diseases', fn (Builder $q): Builder => $q->where('is_chronic', true)),
                        false: fn (Builder $query): Builder => $query->whereDoesntHave('diseases', fn (Builder $q): Builder => $q->where('is_chronic', true)),
                    ),
                TernaryFilter::make('is_refugee')
                    ->label(__('Refugee')),
                DaterangepickerFilter::make('birth_date')
                    ->label(__('Birth Date'))
                    ->useColumn('birth_date'),
                ValueRangeFilter::make('income_range')
                    ->label(__('Monthly Income')),
                TrashedFilter::make(),
            ])
            ->recordActions([
                PrintFamilyMemberReportAction::make()->button(),
                EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__('Export'))
                    ->exporter(FamilyMemberExporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
