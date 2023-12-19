<?php

namespace App\Filament\Resources\DebitResource\Pages;

use App\Filament\Resources\DebitResource;
use App\Models\Debit;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDebits extends ListRecords
{
    protected static string $resource = DebitResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('filament.debit.period.open'))
                ->icon('heroicon-o-plus')
                ->disabled(function () {
                    return Debit::where('status', 'open')->count() >= 2;
                }),

            // Actions\Action::make('closePeriod')
            //     ->label(__('filament.debit.period.close'))
            //     ->button()
            //     ->color('danger')
            //     ->action(function () {
            //         $record = Debit::where('status', 'open')->orderBy('period', 'desc')->get()->last();
            //         $record->status = 'close';
            //         $record->save();
            //     })
            //     ->disabled(function () {
            //         return Debit::where('status', 'open')->count() <= 1;
            //     }),
        ];
    }
}
