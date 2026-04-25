<?php

namespace App\Filament\Resources\Documents\Tables;

use App\Enums\DocumentCategory;
use App\Enums\DocumentType;
use App\Enums\DocumentVisibility;
use App\Filament\Exports\DocumentExporter;
use App\Models\Document;
use App\Models\Family;
use App\Models\FamilyMember;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('family.code')
                    ->label(__('Family'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('charityCase.code')
                    ->label(__('Charity Case Code'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label(__('Category'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable(),
                TextColumn::make('visibility')
                    ->label(__('Visibility'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('issued_at')
                    ->label(__('Issued At'))
                    ->date()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label(__('Expires At'))
                    ->date()
                    ->sortable(),
                IconColumn::make('is_required')
                    ->label(__('Is Required'))
                    ->boolean(),
                IconColumn::make('is_verified')
                    ->label(__('Is Verified'))
                    ->boolean(),
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
            ->filtersFormColumns(6)
            ->filters([
                SelectFilter::make('type')
                    ->options(DocumentType::class)
                    ->searchable()
                    ->preload(),
                SelectFilter::make('category')
                    ->options(DocumentType::class)
                    ->searchable()
                    ->preload(),
                SelectFilter::make('family_id')
                    ->label(__('Family'))
                    ->options(fn (): array => Family::query()->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload(),
                SelectFilter::make('family_member_id')
                    ->label(__('Family Member'))
                    ->options(fn (): array => FamilyMember::query()->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;

                        if (blank($value)) {
                            return $query;
                        }

                        return $query->whereHas('family.familyMembers', fn (Builder $q): Builder => $q->where('family_members.id', $value));
                    }),
                SelectFilter::make('type')
                    ->label(__('Type'))
                    ->options(DocumentType::class)
                    ->searchable(),
                SelectFilter::make('category')
                    ->label(__('Category'))
                    ->options(DocumentCategory::class)
                    ->searchable(),
                SelectFilter::make('visibility')
                    ->label(__('Visibility'))
                    ->options(DocumentVisibility::class)
                    ->searchable(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                self::downloadAction(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__('Export'))
                    ->exporter(DocumentExporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function downloadAction(): Action
    {
        return Action::make('download')
            ->button()
            ->label(__('Download'))
            ->icon('heroicon-o-arrow-down-tray')
            ->url(fn (Document $record): ?string => filled($record->file_path) ? Storage::url($record->file_path) : null)
            ->openUrlInNewTab()
            ->visible(fn (Document $record): bool => filled($record->file_path));
    }
}
