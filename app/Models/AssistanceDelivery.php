<?php

namespace App\Models;

use App\Enums\DeliveryStatus;
use App\Observers\AssistanceDeliveryObserver;
use Database\Factories\AssistanceDeliveryFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(AssistanceDeliveryObserver::class)]
class AssistanceDelivery extends Model
{
    /** @use HasFactory<AssistanceDeliveryFactory> */
    use HasFactory;

    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'delivered_at' => 'datetime',
            'delivery_status' => DeliveryStatus::class,
        ];
    }

    public function assistanceSchedule(): BelongsTo
    {
        return $this->belongsTo(AssistanceSchedule::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
