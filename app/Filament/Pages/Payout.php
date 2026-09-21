<?php

namespace App\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class Payout extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $title = 'Payout';

    protected static string|\UnitEnum|null $navigationGroup = 'Government Payout Workflow';

    protected static ?int $navigationSort = 40;

    protected string $view = 'filament.pages.payout-workflow-stage';

    public function getTitle(): string|Htmlable
    {
        return __('Payout');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Government Payout Workflow');
    }

    public static function getNavigationLabel(): string
    {
        return __('Payout');
    }

    public static function getStageDescription(): string
    {
        return __('Prepare approved assistance for payment, assign payout batches, and track release readiness.');
    }
}
