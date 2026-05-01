<?php

namespace App\Filament\Resources\FamilyMembers\Schemas;

use App\Enums\EducationStatus;
use App\Enums\EmploymentStatus;
use App\Enums\Gender;
use App\Enums\HealthStatus;
use App\Enums\MaritalStatus;
use App\Enums\RelationToHead;
use App\Filament\Resources\Families\RelationManagers\FamilyMembersRelationManager;
use App\Services\EgyptianIDParser;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class FamilyMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Identity'))
                    ->columns(2)
                    ->schema([
                        Select::make('family_id')
                            ->label(__('Family'))
                            ->relationship('family', 'name')
                            ->searchable()
                            ->preload()
                            ->hiddenOn(FamilyMembersRelationManager::class)
                            ->required(),
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->required(),
                        TextInput::make('national_id')
                            ->lazy()
                            ->required()
                            ->afterStateUpdated(fn ($state, Set $set, Get $get) => self::idDetails(get: $get, set: $set))
                            ->unique(table: 'family_members', column: 'national_id', ignoreRecord: true)
                            ->validationMessages(['unique' => __('This National ID is already registered.')])
                            ->label(__('National ID')),
                        Grid::make(2)->schema([
                            ToggleButtons::make('gender')
                                ->label(__('Gender'))
                                ->options(Gender::class)
                                ->inline()
                                ->required(),
                            ToggleButtons::make('is_refugee')
                                ->boolean()->inline()->required()
                                ->label(__('Refugee'))
                                ->default(false),
                        ]),
                        Select::make('relation_to_head')
                            ->label(__('Relation To Head'))
                            ->options(RelationToHead::class)
                            ->searchable()
                            ->preload()
                            ->required(),
                        DatePicker::make('birth_date')
                            ->label(__('Birth Date')),
                        Select::make('marital_status')
                            ->label(__('Marital Status'))
                            ->options(MaritalStatus::class)
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('nationality_id')
                            ->label(__('Nationality'))
                            ->relationship('nationality', 'name')
                            ->searchable()
                            ->preload(),
                        PhoneInput::make('phone')
                            ->label(__('Phone')),
                    ]),
                Section::make(__('Status'))
                    ->columns(2)
                    ->schema([
                        Select::make('education_status')
                            ->label(__('Education Status'))
                            ->options(EducationStatus::class)
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('employment_status')
                            ->label(__('Employment Status'))
                            ->options(EmploymentStatus::class)
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('health_status')
                            ->label(__('Health Status'))
                            ->options(HealthStatus::class)
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        TextInput::make('monthly_income')
                            ->label(__('Monthly Income'))
                            ->required()
                            ->numeric()
                            ->currency()
                            ->default(0.0),
                        Select::make('diseases')
                            ->label(__('Diseases'))
                            ->relationship('diseases', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->visible(fn (Get $get): bool => $get('health_status') === HealthStatus::Unhealthy)
                            ->columnSpanFull(),
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

    public static function idDetails(Get $get, Set $set): void
    {
        $details = EgyptianIDParser::parse($get('national_id'));
        $set('birth_date', data_get($details, 'birth_date'));
        $set('gender', data_get($details, 'gender'));
    }
}
