<?php

namespace App\Filament\Resources\DebitResource\RelationManagers;

use App\Models\Contract;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContractServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'contract_services';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('contract.number')
                    ->label(__('fields.contract_service.contract')),
                TextColumn::make('state.name')
                    ->label(__('fields.contract_service.state')),
                TextColumn::make('service_type.name')
                    ->label(__('fields.contract_service.services')),
                TextColumn::make('program.name')
                    ->label(__('fields.contract_service.programs')),

                TextInputColumn::make('count')
                    ->type('number')
                    ->label(__('fields.contract_service.count'))
                    ->disabled(fn (RelationManager $livewire) => $livewire->ownerRecord->status === 'close'),

                TextInputColumn::make('sum')
                    ->type('number')
                    ->label(__('fields.contract_service.sum'))
                    ->disabled(fn (RelationManager $livewire) => $livewire->ownerRecord->status === 'close'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                // ...
                // Tables\Actions\AttachAction::make()
                //     ->preloadRecordSelect()
            ])
            ->actions([
                // ...
                // Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                // ...
                // Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
