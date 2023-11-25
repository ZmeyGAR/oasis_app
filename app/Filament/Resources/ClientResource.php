<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\State;
use App\Models\Area;
use App\Models\District;
use App\Models\City;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Schema;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('fields.client.name'))
                    ->required(),

                Select::make('type')
                    ->label(__('fields.client.type.name'))
                    ->options(['COMMERCE' => __('fields.client.type.COMMERCE'), 'GOVERMENTAL' => __('fields.client.type.GOVERMENTAL')]),

                Select::make('area_id')
                    ->label(__('fields.client.area'))
                    ->options(fn () => Area::take(10)->get()->pluck('name', 'id')->toArray())
                    ->getSearchResultsUsing(fn (string $search) => Area::where('name', 'LIKE', '%' . $search .  '%')->limit(10)->pluck('name', 'id'))
                    ->getOptionLabelUsing(fn ($value): ?string => Area::find($value)?->name)
                    ->searchable()
                    ->searchDebounce(500)
                    ->reactive()
                    ->preload(),

                Select::make('district_id')
                    ->label(__('fields.client.district'))
                    ->options(function (callable $get) {
                        $record = Area::find($get('area_id'));
                        if (!$record) return [];
                        return $record->districts()->pluck('name', 'id')->toArray();
                    })
                    ->reactive()
                    ->disabled(fn (callable $get): bool => !$get('area_id'))
                    ->preload(),

                Select::make('city_id')
                    ->label(__('fields.client.city'))
                    ->options(function (callable $get) {
                        $record = District::find($get('district_id'));
                        if (!$record) return [];
                        return $record->cities()->pluck('name', 'id')->toArray();
                    })
                    ->reactive()
                    ->disabled(fn (callable $get): bool => !$get('district_id'))
                    ->preload(),

                TextInput::make('address')
                    ->label(__('fields.client.address')),

                TextInput::make('IIK')
                    ->label(__('fields.client.IIK')),

                TextInput::make('BIN')
                    ->label(__('fields.client.BIN')),

                TextInput::make('BIK')
                    ->label(__('fields.client.BIK')),

                TextInput::make('BANK')
                    ->label(__('fields.client.BANK')),

                TextInput::make('KBE')
                    ->label(__('fields.client.KBE')),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('fields.client.name'))
                    ->searchable(),

                TextColumn::make('type')
                    ->label(__('fields.client.type.name'))
                    ->enum(['COMMERCE' => __('fields.client.type.COMMERCE'), 'GOVERMENTAL' => __('fields.client.type.GOVERMENTAL')])
                    ->sortable(),

                TextColumn::make('city.area.name')
                    ->label(__('fields.client.area')),
                TextColumn::make('city.district.name')
                    ->label(__('fields.client.district')),
                TextColumn::make('city.name')
                    ->label(__('fields.client.city')),
                TextColumn::make('address')
                    ->label(__('fields.client.address')),

                TextColumn::make('IIK')
                    ->label(__('fields.client.IIK')),
                TextColumn::make('BIN')
                    ->label(__('fields.client.BIN')),
                TextColumn::make('BIK')
                    ->label(__('fields.client.BIK')),
                TextColumn::make('BANK')
                    ->label(__('fields.client.BANK')),
                TextColumn::make('KBE')
                    ->label(__('fields.client.KBE')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListClients::route('/'),
            // 'create' => Pages\CreateClient::route('/create'),
            // 'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getModelLabel(): string
    {
        return __('filament.pages.client.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.pages.client.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.client.plural_label');
    }

    // protected static function getNavigationGroup(): ?string
    // {
    //     return __('filament.navigation.guide.label');
    // }
}
