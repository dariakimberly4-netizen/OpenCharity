<?php

namespace App\Filament\Resources\CharityCases\Pages;

use App\Enums\VisitStatus;
use App\Enums\VisitStatusCase;
use App\Filament\Resources\CharityCases\CharityCaseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCharityCase extends CreateRecord
{
    protected static string $resource = CharityCaseResource::class;
}
