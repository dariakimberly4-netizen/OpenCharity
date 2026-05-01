<?php

namespace App\Traits;

use App\Models\Notable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasNotables
{
    public function notables(): MorphMany
    {
        return $this->morphMany(Notable::class, 'notable')->latest();
    }

    public function addNote(string $note, ?Model $creator = null): Notable
    {
        $data = ['note' => $note];

        if ($creator) {
            $data['creator_type'] = $creator->getMorphClass();
            $data['creator_id'] = $creator->getKey();
        }

        return $this->notables()->create($data);
    }

    public function hasNotes(): bool
    {
        return $this->notables()->exists();
    }

    public function notesCount(): int
    {
        return $this->notables()->count();
    }
}
