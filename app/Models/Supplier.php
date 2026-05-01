<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'phones' => 'array',
        ];
    }

    public function assistanceTypes(): BelongsToMany
    {
        return $this->belongsToMany(AssistanceType::class);
    }

    public function assistanceDeliveries(): HasMany
    {
        return $this->hasMany(AssistanceDelivery::class);
    }
}
