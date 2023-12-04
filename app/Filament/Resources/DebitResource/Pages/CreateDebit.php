<?php

namespace App\Filament\Resources\DebitResource\Pages;

use App\Filament\Resources\DebitResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDebit extends CreateRecord
{
    protected static string $resource = DebitResource::class;
}
