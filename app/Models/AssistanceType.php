<?php

namespace App\Models;

use App\Enums\AssistanceUnitType;
use Database\Factories\AssistanceTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MohamedSaid\Referenceable\Traits\HasReference;

class AssistanceType extends Model
{
    /** @use HasFactory<AssistanceTypeFactory> */
    use HasFactory;

    use HasReference;

    protected string $referenceColumn = 'code';

    protected string $referencePrefix = 'AT';

    protected int $referenceLength = 5;

    protected function casts(): array
    {
        return [
            'unit_type' => AssistanceUnitType::class,
            'is_recurring_allowed' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function assistanceSchedules(): HasMany
    {
        return $this->hasMany(AssistanceSchedule::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class);
    }
}
