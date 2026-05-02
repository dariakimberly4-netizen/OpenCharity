<?php

namespace App\Models;

use App\Enums\EducationStatus;
use App\Enums\EmploymentStatus;
use App\Enums\Gender;
use App\Enums\HealthStatus;
use App\Enums\MaritalStatus;
use App\Enums\RelationToHead;
use Database\Factories\FamilyMemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class FamilyMember extends Model
{
    /** @use HasFactory<FamilyMemberFactory> */
    use HasFactory;

    use SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $member): void {
            if ($member->code) {
                return;
            }

            DB::transaction(function () use ($member): void {
                $family = Family::lockForUpdate()->find($member->family_id);
                $familySeq = explode('-', $family->code)[1];
                $next = self::withTrashed()->where('family_id', $member->family_id)->count() + 1;
                $member->code = sprintf('M-%s-%04d', $familySeq, $next);
            });
        });
    }

    protected function casts(): array
    {
        return [
            'relation_to_head' => RelationToHead::class,
            'gender' => Gender::class,
            'birth_date' => 'date',
            'marital_status' => MaritalStatus::class,
            'education_status' => EducationStatus::class,
            'employment_status' => EmploymentStatus::class,
            'health_status' => HealthStatus::class,
            'monthly_income' => 'decimal:2',
            'is_refugee' => 'boolean',
        ];
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function diseases(): BelongsToMany
    {
        return $this->belongsToMany(Disease::class);
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function charityCases(): HasMany
    {
        return $this->hasMany(CharityCase::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
