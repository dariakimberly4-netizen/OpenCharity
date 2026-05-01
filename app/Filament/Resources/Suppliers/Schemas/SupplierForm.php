<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use App\Filament\Resources\AssistanceTypes\Schemas\AssistanceTypeForm;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use SalemAljebaly\FilamentMapPicker\MapPicker;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Basic'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->columnSpanFull(),
                        Select::make('assistanceTypes')
                            ->label(__('Assistance Types'))
                            ->relationship('assistanceTypes', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->createOptionForm(fn (Schema $schema) => AssistanceTypeForm::configure($schema))
                            ->columnSpanFull(),
                    ]),
                Section::make(__('Phones'))
                    ->schema([
                        Repeater::make('phones')
                            ->label(__('Phones'))
                            ->simple(
                                TextInput::make('number')
                                    ->label(__('Phone Number'))
                                    ->tel()
                                    ->required()
                            )
                            ->defaultItems(1)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make(__('Location'))
                    ->schema([
                        Textarea::make('address')
                            ->label(__('Address'))
                            ->columnSpanFull(),
                        Hidden::make('latitude'),
                        Hidden::make('longitude'),
                        MapPicker::make('location')
                            ->label(__('Location'))
                            ->latlngFields('latitude', 'longitude')
                            ->searchable()
                            ->collapsibleSearch()
                            ->draggable()
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
            ]);
    }
}
