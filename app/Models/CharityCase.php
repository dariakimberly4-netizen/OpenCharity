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
use Illuminate\Support\Facades\DB;

#[ObservedBy(CharityCaseObserver::class)]
class CharityCase extends Model
{
    /** @use HasFactory<CharityCaseFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $appends = ['full_identifier'];

    protected static function booted(): void
    {
        static::creating(function (self $case): void {
            if ($case->code) {
                return;
            }

            DB::transaction(function () use ($case): void {
                $member = FamilyMember::lockForUpdate()->find($case->family_member_id);
                $family = Family::find($case->family_id);

                $familySeq = explode('-', $family->code)[1];
                $memberSeq = explode('-', $member->code)[2];

                $next = self::withTrashed()
                    ->where('family_member_id', $case->family_member_id)
                    ->count() + 1;

                $case->code = sprintf('C-%s-%s-%04d', $familySeq, $memberSeq, $next);
            });
        });
    }

    public function fullIdentifier(): Attribute
    {
        return Attribute::get(fn () => "({$this->code}) - {$this->familyMember->name}");
    }

    public function visit(): HasOne
    {
        return $this->hasOne(Visit::class)->latestOfMany();
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

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
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
