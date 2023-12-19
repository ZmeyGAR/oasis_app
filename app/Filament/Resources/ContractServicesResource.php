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
use Filament\Forms\Components\Card;
use App\Rules\UniqueContractServicesColumnsTogether;
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
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Validation\ValidationException;
use Closure;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Model;

class ContractServicesResource extends Resource
{
    protected static ?string $model = ContractServices::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->columns(2)->schema([
                    Select::make('contract_id')
                        ->label(__('fields.contract_service.contract'))
                        ->options(fn () => Contract::take(10)->get()->pluck('number', 'id'))
                        ->getSearchResultsUsing(fn (string $search) => Contract::where('number', 'LIKE', '%' . $search .  '%')->limit(10)->pluck('number', 'id'))
                        ->getOptionLabelUsing(fn ($value): ?string => Contract::find($value)?->number)
                        ->searchable()
                        ->searchDebounce(500)
                        ->reactive()
                        ->required()
                        ->rules([
                            function (Closure $get, ?Model $record) {
                                $exclude_record_id = null;
                                if ($record and $record?->exists) $exclude_record_id = $record->id;
                                return new UniqueContractServicesColumnsTogether([
                                    'contract_id'           => $get('contract_id'),
                                    'service_type_id'       => $get('service_type_id'),
                                    'program_id'            => $get('program_id'),
                                    'state_id'              => $get('state_id'),
                                ], $exclude_record_id);
                            },
                        ])
                        ->columnSpan(2),
                ]),


                Card::make()->columns(2)->schema([

                    Select::make('service_type_id')
                        ->label(__('fields.contract_service.services'))
                        ->options(fn () => ServiceType::take(10)->get()->pluck('name', 'id'))
                        ->getSearchResultsUsing(fn (string $search) => ServiceType::where('name', 'LIKE', '%' . $search .  '%')->limit(10)->pluck('name', 'id'))
                        ->getOptionLabelUsing(fn ($value): ?string => ServiceType::find($value)?->name)
                        ->searchable()
                        ->searchDebounce(500)
                        ->reactive()
                        ->rules([
                            function (Closure $get, ?Model $record) {
                                $exclude_record_id = null;
                                if ($record and $record?->exists) $exclude_record_id = $record->id;
                                return new UniqueContractServicesColumnsTogether([
                                    'contract_id'           => $get('contract_id'),
                                    'service_type_id'       => $get('service_type_id'),
                                    'program_id'            => $get('program_id'),
                                    'state_id'              => $get('state_id'),
                                ], $exclude_record_id);
                            },
                        ]),

                    Select::make('program_id')
                        ->label(__('fields.contract_service.programs'))
                        ->options(fn () => Program::take(10)->get()->pluck('name', 'id'))
                        ->getSearchResultsUsing(fn (string $search) => Program::where('name', 'LIKE', '%' . $search .  '%')->limit(10)->pluck('name', 'id'))
                        ->getOptionLabelUsing(fn ($value): ?string => Program::find($value)?->name)
                        ->searchable()
                        ->searchDebounce(500)
                        ->reactive()
                        ->rules([
                            function (Closure $get, ?Model $record) {
                                $exclude_record_id = null;
                                if ($record and $record?->exists) $exclude_record_id = $record->id;
                                return new UniqueContractServicesColumnsTogether([
                                    'contract_id'           => $get('contract_id'),
                                    'service_type_id'       => $get('service_type_id'),
                                    'program_id'            => $get('program_id'),
                                    'state_id'              => $get('state_id'),
                                ], $exclude_record_id);
                            },
                        ]),

                    Select::make('state_id')
                        ->label(__('fields.contract_service.state'))
                        ->options(fn () => State::take(10)->get()->pluck('name', 'id'))
                        ->getSearchResultsUsing(fn (string $search) => State::where('name', 'LIKE', '%' . $search .  '%')->limit(10)->pluck('name', 'id'))
                        ->getOptionLabelUsing(fn ($value): ?string => State::find($value)?->name)
                        ->searchable()
                        ->searchDebounce(500)
                        ->required()
                        ->rules([
                            function (Closure $get, ?Model $record) {
                                $exclude_record_id = null;
                                if ($record and $record?->exists) $exclude_record_id = $record->id;
                                return new UniqueContractServicesColumnsTogether([
                                    'contract_id'           => $get('contract_id'),
                                    'service_type_id'       => $get('service_type_id'),
                                    'program_id'            => $get('program_id'),
                                    'state_id'              => $get('state_id'),
                                ], $exclude_record_id);
                            },
                        ]),

                    TextInput::make('count')
                        ->label(__('fields.contract_service.count'))
                        ->numeric()
                        ->default(0)
                        ->required(),
                ]),

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
                TextColumn::make('service_type.name')
                    ->label(__('fields.contract_service.services')),
                TextColumn::make('program.name')
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
            'create' => Pages\CreateContractServices::route('/create'),
            'edit' => Pages\EditContractServices::route('/{record}/edit'),
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
