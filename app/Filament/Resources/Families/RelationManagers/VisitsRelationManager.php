<?php

namespace App\Filament\Resources\Families\RelationManagers;

use App\Enums\VisitStatus;
use App\Enums\VisitType;
use App\Filament\Resources\Visits\Schemas\VisitForm;
use App\Filament\Resources\Visits\Tables\VisitsTable;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\SlideOverPosition;
use Filament\Support\Enums\Width;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    protected static ?string $title = 'Visits';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(self::$title);
    }

    public static function getPluralModelLabel(): ?string
    {
        return __(self::$title);
    }

    public function form(Schema $schema): Schema
    {
        return VisitForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return VisitsTable::configure($table)
            ->modelLabel(__('Visit'))
            ->recordActions([
                EditAction::make()->slideOver()->modalWidth(Width::Full),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->slideOver()
                    ->modalWidth(Width::Full),
            ]);
    }
}
