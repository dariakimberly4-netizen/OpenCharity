<?php

namespace App\Models;

use App\Enums\CasePriority;
use App\Enums\CaseStatus;
use App\Enums\VisitStatus;
use App\Enums\VisitStatusCase;
use App\Observers\CharityCaseObserver;
use Database\Factories\CharityCaseFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use MohamedSaid\Referenceable\Traits\HasReference;

#[ObservedBy(CharityCaseObserver::class)]
class CharityCase extends Model
{
    /** @use HasFactory<CharityCaseFactory> */
    use HasFactory;

    use HasReference;
    use SoftDeletes;

    protected string $referenceColumn = 'code';
    protected $referenceStrategy = 'sequential';
    protected $referenceSequential = [
        'start' => 1,
        'min_digits' => 4,
        'reset_frequency' => 'never',
    ];
    protected $appends = ['full_identifier'];

    public function getReferencePrefix(): string
    {
        return 'C-' . explode('-', $this->family->code)[1] . '-' . explode('-', $this->familyMember->code)[1];
    }

    public function visit(): HasOne
    {
        return $this->hasOne(Visit::class)->latestOfMany();
    }

    public function fullIdentifier(): Attribute
    {
        return Attribute::get(fn() => "({$this->code}) - {$this->familyMember->name}");
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function familyMember(): BelongsTo
    {
        return $this->belongsTo(FamilyMember::class);
    }

    public function caseType(): BelongsTo
    {
        return $this->belongsTo(CaseType::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function assistanceSchedules(): HasMany
    {
        return $this->hasMany(AssistanceSchedule::class);
    }

    public function donationTargets(): HasMany
    {
        return $this->hasMany(DonationTarget::class);
    }

    public function syncVisitDates(): void
    {
        $this->last_visit_at = $this->visits()
            ->where('status', VisitStatus::Completed)
            ->whereNotNull('visited_at')
            ->max('visited_at');

        $this->next_visit_at = $this->visits()
            ->where('status', VisitStatus::Scheduled)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', now())
            ->min('scheduled_at');

        $this->saveQuietly();
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    protected function casts(): array
    {
        return [
            'priority' => CasePriority::class,
            'status' => CaseStatus::class,
            'visit_status' => VisitStatusCase::class,
            'registered_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'approved_at' => 'datetime',
            'closed_at' => 'datetime',
            'last_visit_at' => 'datetime',
            'next_visit_at' => 'datetime',
            'requested_amount' => 'decimal:2',
            'approved_amount' => 'decimal:2',
        ];
    }
}
