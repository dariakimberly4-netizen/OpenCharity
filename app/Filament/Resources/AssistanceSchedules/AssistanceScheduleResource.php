<?php

namespace App\Filament\Resources\AssistanceSchedules;

use App\Filament\Resources\AssistanceSchedules\Pages\CreateAssistanceSchedule;
use App\Filament\Resources\AssistanceSchedules\Pages\EditAssistanceSchedule;
use App\Filament\Resources\AssistanceSchedules\Pages\ListAssistanceSchedules;
use App\Filament\Resources\AssistanceSchedules\Pages\ViewAssistanceSchedule;
use App\Filament\Resources\AssistanceSchedules\RelationManagers\AssistanceDeliveriesRelationManager;
use App\Filament\Resources\AssistanceSchedules\RelationManagers\NotesRelationManager;
use App\Filament\Resources\AssistanceSchedules\Schemas\AssistanceScheduleForm;
use App\Filament\Resources\AssistanceSchedules\Schemas\AssistanceScheduleInfolist;
use App\Filament\Resources\AssistanceSchedules\Tables\AssistanceSchedulesTable;
use App\Models\AssistanceSchedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssistanceScheduleResource extends Resource
{
    protected static ?string $model = AssistanceSchedule::class;

    protected static ?string $recordTitleAttribute = 'code';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|\UnitEnum|null $navigationGroup = 'Assistance';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return AssistanceScheduleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssistanceScheduleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssistanceSchedulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AssistanceDeliveriesRelationManager::class,
            NotesRelationManager::class,
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Assistance Schedule');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Assistance Schedules');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Assistance');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssistanceSchedules::route('/'),
            'create' => CreateAssistanceSchedule::route('/create'),
            'view' => ViewAssistanceSchedule::route('/{record}'),
            'edit' => EditAssistanceSchedule::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('view', ['record' => $record]);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
