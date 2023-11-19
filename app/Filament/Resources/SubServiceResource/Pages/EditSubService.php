<?php

namespace App\Filament\Resources\SubServiceResource\Pages;

use App\Filament\Resources\SubServiceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubService extends EditRecord
{
    protected static string $resource = SubServiceResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
