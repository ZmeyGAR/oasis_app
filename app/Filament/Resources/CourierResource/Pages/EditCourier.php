<?php

namespace App\Filament\Resources\CourierResource\Pages;

use App\Filament\Resources\CourierResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourier extends EditRecord
{
    protected static string $resource = CourierResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
