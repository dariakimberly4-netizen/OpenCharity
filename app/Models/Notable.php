<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notable extends Model
{
    protected $fillable = [
        'note',
        'notable_type',
        'notable_id',
        'creator_type',
        'creator_id',
    ];

    public function notable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): MorphTo
    {
        return $this->morphTo();
    }
}
