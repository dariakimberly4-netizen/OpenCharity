<?php

namespace App\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class Assessment extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $title = 'Assessment';

    protected static string|\UnitEnum|null $navigationGroup = 'Government Payout Workflow';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.payout-workflow-stage';

    public function getTitle(): string|Htmlable
    {
        return __('Assessment');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Government Payout Workflow');
    }

    public static function getNavigationLabel(): string
    {
        return __('Assessment');
    }

    public static function getStageDescription(): string
    {
        return __('Evaluate eligibility, needs, supporting records, and recommended assistance before approval.');
    }
}
