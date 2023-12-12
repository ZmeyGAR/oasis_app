<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractServicesResource\Pages;
use App\Filament\Resources\ContractServicesResource\RelationManagers;
use App\Models\Contract;
use App\Models\ContractServices;
use App\Models\Program;
use App\Models\ServiceType;
use App\Models\ProgramType;
use App\Models\State;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Component;

class ContractServicesResource extends Resource
{
    protected static ?string $model = ContractServices::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('contract_id')
                    ->label(__('fields.contract_service.contract'))
                    ->options(fn () => Contract::take(10)->get()->pluck('number', 'id'))
                    ->getSearchResultsUsing(fn (string $search) => Contract::where('number', 'LIKE', '%' . $search .  '%')->limit(10)->pluck('number', 'id'))
                    ->getOptionLabelUsing(fn ($value): ?string => Contract::find($value)?->number)
                    ->searchable()
                    ->searchDebounce(500)
                    ->reactive()
                    ->required(),

                Select::make('services')
                    ->label(__('fields.contract_service.services'))
                    ->relationship('services', 'name')
                    ->multiple()
                    ->options(fn () => ServiceType::take(10)->get()->pluck('name', 'id'))
                    ->getSearchResultsUsing(fn (string $search) => ServiceType::where('name', 'LIKE', '%' . $search .  '%')->limit(10)->pluck('name', 'id'))
                    ->getOptionLabelUsing(fn ($value): ?string => ServiceType::find($value)?->name)
                    ->searchable()
                    ->searchDebounce(500)
                    ->reactive(),

                Select::make('programs')
                    ->label(__('fields.contract_service.programs'))
                    ->relationship('programs', 'name')
                    ->multiple()
                    ->options(fn () => Program::take(10)->get()->pluck('name', 'id'))
                    ->getSearchResultsUsing(fn (string $search) => Program::where('name', 'LIKE', '%' . $search .  '%')->limit(10)->pluck('name', 'id'))
                    ->getOptionLabelUsing(fn ($value): ?string => Program::find($value)?->name)
                    ->searchable()
                    ->searchDebounce(500)
                    ->reactive(),

                Select::make('state_id')
                    ->label(__('fields.contract_service.state'))
                    ->options(fn () => State::take(10)->get()->pluck('name', 'id'))
                    ->getSearchResultsUsing(fn (string $search) => State::where('name', 'LIKE', '%' . $search .  '%')->limit(10)->pluck('name', 'id'))
                    ->getOptionLabelUsing(fn ($value): ?string => State::find($value)?->name)
                    ->searchable()
                    ->searchDebounce(500)
                    ->required(),

                TextInput::make('count')
                    ->label(__('fields.contract_service.count'))
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contract.number')
                    ->label(__('fields.contract_service.contract')),
                TextColumn::make('state.name')
                    ->label(__('fields.contract_service.state')),
                TagsColumn::make('services.name')
                    ->label(__('fields.contract_service.services')),
                TagsColumn::make('programs.name')
                    ->label(__('fields.contract_service.programs')),
                TextColumn::make('count')
                    ->label(__('fields.contract_service.count')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContractServices::route('/'),
            // 'create' => Pages\CreateContractServices::route('/create'),
            // 'edit' => Pages\EditContractServices::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament.pages.contract_service.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.pages.contract_service.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.contract_service.plural_label');
    }

    // protected static function getNavigationGroup(): ?string
    // {
    //     return __('filament.navigation.state.label');
    // }


}
