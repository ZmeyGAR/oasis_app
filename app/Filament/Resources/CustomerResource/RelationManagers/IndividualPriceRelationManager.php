<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Models\Product;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\Column;

class IndividualPriceRelationManager extends RelationManager
{
    protected static string $relationship = 'individual_price';

    protected static ?string $recordTitleAttribute = 'id';

    public static function getTitle(): string
    {
        return __('filament.pages.individual_price.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('price')
                    ->label(__('fields.individual_price.price'))
                    ->required()
                    ->minValue(0.01)
                    ->mask(
                        fn (Mask $mask) => $mask
                            ->patternBlocks([
                                'money' => fn (Mask $mask) => $mask
                                    ->numeric()
                                    ->thousandsSeparator(',')
                                    ->decimalSeparator('.')
                                    ->signed(true),
                            ])
                            ->pattern('₸ money'),
                    )
                    ->required(),

                Select::make('product_id')
                    ->label(__('fields.individual_price.product_name'))
                    ->options(Product::all()->pluck('name', 'id'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('price')
                    ->label(__('fields.individual_price.price'))
                    ->money('kzt', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('product_id')
                    ->label(__('fields.individual_price.product_name'))
                    ->formatStateUsing(function ($state) {
                        return Product::find((int)$state)->name;
                    })
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
