<?php

namespace App\Observers;

use App\Enums\VisitStatus;
use App\Models\Visit;

class VisitObserver
{
    public function creating(Visit $visit): void
    {
        $visit->family_id = $visit->charityCase?->family_id;
        $visit->family_member_id = $visit->charityCase?->family_member_id;
        if (!$visit->status) {
            $visit->status = VisitStatus::Scheduled;
        }
    }
    public function saved(Visit $visit): void
    {
        $visit->charityCase?->syncVisitDates();
    }

    public function deleted(Visit $visit): void
    {
        $visit->charityCase?->syncVisitDates();
    }
}
