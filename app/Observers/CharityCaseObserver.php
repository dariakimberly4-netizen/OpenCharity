<?php

namespace App\Observers;

use App\Enums\CaseStatus;
use App\Models\CharityCase;

class CharityCaseObserver
{
    public function creating(CharityCase $charityCase): void
    {
        $charityCase->registered_at ??= now();
    }

    public function updating(CharityCase $charityCase): void
    {
        if (! $charityCase->isDirty('status')) {
            return;
        }

        $status = $charityCase->status;

        if (in_array($status, [CaseStatus::PendingReview]) && $charityCase->reviewed_at === null) {
            $charityCase->reviewed_at = now();
        }

        if ($status === CaseStatus::Approved && $charityCase->approved_at === null) {
            $charityCase->approved_at = now();
        }

        if (in_array($status, [CaseStatus::Completed]) && $charityCase->closed_at === null) {
            $charityCase->closed_at = now();
        }
    }
}
