<?php

namespace App\Models;

use App\Enums\FundingStatus;
use App\Enums\ScheduleFrequency;
use App\Enums\ScheduleStatus;
use App\Observers\AssistanceScheduleObserver;
use App\Traits\HasNotables;
use Database\Factories\AssistanceScheduleFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(AssistanceScheduleObserver::class)]
class AssistanceSchedule extends Model
{
    /** @use HasFactory<AssistanceScheduleFactory> */
    use HasFactory;

    use HasNotables;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'parent_schedule_id' => 'integer',
            'scheduled_date' => 'date',
            'end_date' => 'date',
            'scheduled_time' => 'datetime:H:i',
            'amount' => 'decimal:2',
            'quantity' => 'decimal:2',
            'occurrence_number' => 'integer',
            'frequency' => ScheduleFrequency::class,
            'status' => ScheduleStatus::class,
            'funding_status' => FundingStatus::class,
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_schedule_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_schedule_id');
    }

    public function charityCase(): BelongsTo
    {
        return $this->belongsTo(CharityCase::class);
    }

    public function assistanceType(): BelongsTo
    {
        return $this->belongsTo(AssistanceType::class);
    }

    public function assistanceDeliveries(): HasMany
    {
        return $this->hasMany(AssistanceDelivery::class);
    }

    public function isParent(): bool
    {
        return $this->parent_schedule_id === null && $this->isRecurring();
    }

    public function isChild(): bool
    {
        return $this->parent_schedule_id !== null;
    }

    public function isRecurring(): bool
    {
        return $this->frequency !== ScheduleFrequency::Once;
    }
}
