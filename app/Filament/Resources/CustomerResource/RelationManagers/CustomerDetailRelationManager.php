<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'details';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('ownership')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('BIN_IIN')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('bank_account')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('legal_address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('KBE')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('ownership'),
                Tables\Columns\TextColumn::make('BIN_IIN'),
                Tables\Columns\TextColumn::make('bank_account'),
                Tables\Columns\TextColumn::make('legal_address'),
                Tables\Columns\TextColumn::make('KBE'),
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

    public static function canViewForRecord(Model $ownerRecord): bool
    {
        return $ownerRecord->person_type === 'LEGAL_PERSON';
    }
}
