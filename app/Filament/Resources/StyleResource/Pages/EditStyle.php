<?php

namespace App\Filament\Resources\StyleResource\Pages;

use App\Filament\Resources\StyleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStyle extends EditRecord
{
    protected static string $resource = StyleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
