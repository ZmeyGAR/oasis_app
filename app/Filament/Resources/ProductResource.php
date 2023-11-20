<?php

namespace App\Filament\Resources;

use Closure;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Livewire;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'products';

    protected static ?string $slug = 'shop/products';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Card::make()
                    ->schema([

                        Group::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('fields.product.name'))
                                    ->maxValue(255)
                                    ->autofocus()
                                    ->required(),
                                TextInput::make('wp_id')
                                    ->label(__('fields.product.wp_id'))
                                    ->type('number'),
                                Select::make('type')
                                    ->label(__('fields.product.type'))
                                    ->options(['PRODUCT' => 'Продукт', 'SERVICE' => 'Услуга',])
                                    ->required()
                                    ->reactive(),
                            ])
                            ->columnSpan(['lg' => 1]),

                        Group::make()
                            ->schema([
                                TextInput::make('price')
                                    ->label(__('fields.product.price'))
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
                                    ->default(0),

                                TextInput::make('quantity')
                                    ->label(__('fields.product.quantity'))
                                    ->default(0)
                                    ->hidden(fn (Closure $get) => $get('type') !== 'PRODUCT'),
                            ])
                            ->columnSpan(['lg' => 1]),

                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => 2]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('type')
                    ->label(__('fields.product.type'))
                    ->formatStateUsing(function ($state) {
                        $types = [
                            'PRODUCT' => 'Продукт',
                            'SERVICE' => 'Услуга',
                        ];
                        return $types[$state];
                    })
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('fields.product.name'))
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label(__('fields.product.price'))
                    ->sortable()
                    ->money('KZT', true),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('fields.product.quantity'))
                    ->sortable()
                    ->getStateUsing(function (Model $record) {
                        return ($record->type !== 'SERVICE') ? $record->quantity : 'Ꝏ';
                    }),

                Tables\Columns\TextColumn::make('wp_id')
                    ->label(__('fields.product.wp_id'))
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->sortable()
                    ->toggleable(),

            ])
            ->filters([])
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/view/{record}'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament.pages.product.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.pages.product.plural_label');
    }


    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.product.plural_label');
    }

    protected static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.shop.label');
    }
}
