<?php

namespace App\Filament\Resources\ContractServicesResource\Pages;

use App\Filament\Resources\ContractServicesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContractServices extends ListRecords
{
    protected static string $resource = ContractServicesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
