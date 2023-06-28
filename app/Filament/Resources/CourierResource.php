<?php

namespace App\Filament\Resources;


use App\Filament\Resources\CourierResource\Pages;
use App\Filament\Resources\CourierResource\RelationManagers;

use App\Models\Courier;
use App\Models\User;
use App\Models\Car;


use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Forms\Components\Select;


use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourierResource extends Resource
{
    protected static ?string $model = Courier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('user_id')
                    ->label(__('fields.courier.users'))
                    ->searchable()
                    ->searchDebounce(1000)
                    ->getOptionLabelUsing(function ($value) {
                        if ($value) {
                            return User::find($value)?->name;
                        }
                    })
                    ->getSearchResultsUsing(function ($query) {
                        if (strlen($query) >= 5) {
                            return User::where('name', 'LIKE', "%$query%")
                                ->get()
                                ->pluck('name', 'id');
                        }
                        return [];
                    }),


                Select::make('car_id')
                    ->label(__('fields.courier.transport'))
                    ->searchable()
                    ->searchDebounce(1000)
                    ->options(fn () => Car::get()->pluck('name', 'id'))
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListCouriers::route('/'),
            'create' => Pages\CreateCourier::route('/create'),
            'view' => Pages\ViewCourier::route('/{record}/view'),
            'edit' => Pages\EditCourier::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament.pages.courier.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.pages.courier.plural_label');
    }


    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.courier.plural_label');
    }

    protected static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.car.label');
    }
}
