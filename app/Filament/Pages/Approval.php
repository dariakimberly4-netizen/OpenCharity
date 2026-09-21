<?php

namespace App\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class Approval extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static ?string $title = 'Approval';

    protected static string|\UnitEnum|null $navigationGroup = 'Government Payout Workflow';

    protected static ?int $navigationSort = 30;

    protected string $view = 'filament.pages.payout-workflow-stage';

    public function getTitle(): string|Htmlable
    {
        return __('Approval');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Government Payout Workflow');
    }

    public static function getNavigationLabel(): string
    {
        return __('Approval');
    }

    public static function getStageDescription(): string
    {
        return __('Review assessed cases and record the authorized decision before payout preparation.');
    }
}
