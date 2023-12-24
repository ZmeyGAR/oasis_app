<?php

namespace App\Filament\Resources\ContractResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'sub_contracts';
    protected static ?string $recordTitleAttribute = 'number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->label(__('fields.sub_contract.number'))
                    ->required()
                    ->maxLength(255),

                DatePicker::make('date_start')
                    ->label(__('fields.sub_contract.date_start')),
                DatePicker::make('date_end')
                    ->label(__('fields.sub_contract.date_end')),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label(__('fields.sub_contract.number')),
                Tables\Columns\TextColumn::make('contract.number')
                    ->label(__('fields.sub_contract.contract.number')),
                Tables\Columns\TextColumn::make('date_start')
                    ->label(__('fields.sub_contract.date_start')),
                Tables\Columns\TextColumn::make('date_end')
                    ->label(__('fields.sub_contract.date_end')),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getTitle(): string
    {
        return __('filament.sub_contract.title');
    }
}
