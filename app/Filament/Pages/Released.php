<?php

namespace App\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class Released extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;

    protected static ?string $title = 'Released';

    protected static string|\UnitEnum|null $navigationGroup = 'Government Payout Workflow';

    protected static ?int $navigationSort = 50;

    protected string $view = 'filament.pages.payout-workflow-stage';

    public function getTitle(): string|Htmlable
    {
        return __('Released');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Government Payout Workflow');
    }

    public static function getNavigationLabel(): string
    {
        return __('Released');
    }

    public static function getStageDescription(): string
    {
        return __('Monitor completed releases, claimant status, and final assistance disbursement records.');
    }
}
