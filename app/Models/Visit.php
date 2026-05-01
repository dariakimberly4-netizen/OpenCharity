<?php

namespace App\Models;

use App\Enums\VisitStatus;
use App\Enums\VisitType;
use App\Observers\VisitObserver;
use Database\Factories\VisitFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(VisitObserver::class)]
class Visit extends Model
{
    /** @use HasFactory<VisitFactory> */
    use HasFactory;

    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'visit_type' => VisitType::class,
            'status' => VisitStatus::class,
            'scheduled_at' => 'datetime',
            'visited_at' => 'datetime',
            'next_visit_at' => 'datetime',
        ];
    }

    public function netAmount(): Attribute
    {
        return Attribute::get(
            fn () => $this->visitIncomes->sum('amount') - $this->visitExpenses->sum('amount')
        );
    }

    public function charityCase(): BelongsTo
    {
        return $this->belongsTo(CharityCase::class);
    }

    public function visitIncomes(): HasMany
    {
        return $this->hasMany(VisitIncome::class);
    }

    public function visitExpenses(): HasMany
    {
        return $this->hasMany(VisitExpense::class);
    }
}
