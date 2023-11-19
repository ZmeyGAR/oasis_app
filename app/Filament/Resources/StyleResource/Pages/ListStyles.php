<?php

namespace App\Filament\Resources\StyleResource\Pages;

use App\Filament\Resources\StyleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStyles extends ListRecords
{
    protected static string $resource = StyleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
