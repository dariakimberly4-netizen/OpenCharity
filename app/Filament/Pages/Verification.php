<?php

namespace App\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class Verification extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $title = 'Verification';

    protected static string|\UnitEnum|null $navigationGroup = 'Government Payout Workflow';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.payout-workflow-stage';

    public function getTitle(): string|Htmlable
    {
        return __('Verification');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Government Payout Workflow');
    }

    public static function getNavigationLabel(): string
    {
        return __('Verification');
    }

    public static function getStageDescription(): string
    {
        return __('Review submitted applications, validate identity and supporting documents, and mark cases ready for assessment.');
    }
}
