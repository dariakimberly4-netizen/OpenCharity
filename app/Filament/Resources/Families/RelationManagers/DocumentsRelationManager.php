<?php

namespace App\Filament\Resources\Families\RelationManagers;

use App\Filament\Resources\Documents\Schemas\DocumentForm;
use App\Filament\Resources\Documents\Tables\DocumentsTable;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Documents';

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
        return DocumentForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return DocumentsTable::configure($table)

            ->modelLabel(__('Document'))
            ->recordActions([
                DocumentsTable::downloadAction(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->slideOver()
                    ->modalWidth(Width::ScreenExtraLarge)
                    ->fillForm(function (array $data): array {
                        $data['family_id'] = $this->getOwnerRecord()->getKey();

                        return $data;
                    }),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
