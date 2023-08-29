<?php

namespace App\Filament\Resources;

use Closure;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Customer;
use App\Models\CustomerShiping;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Illuminate\Support\HtmlString;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Stevebauman\Purify\Facades\Purify;


use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Укажите адрес доставки, используя справочники или укажите новый адрес доставки')
                    ->schema([
                        Grid::make()
                            ->schema([

                                Grid::make()
                                    ->schema([


                                        Select::make('customer_and_shiping')
                                            ->hint('ПОИСК В СПРАВОЧНИКЕ')
                                            ->helperText('Поиск по адресу доставки')
                                            ->reactive()
                                            ->dehydrated(false)
                                            ->allowHtml()
                                            ->searchable()
                                            ->searchDebounce(1000)
                                            ->getSearchResultsUsing(function ($query) {
                                                if (strlen($query) >= 3) {

                                                    $customer_shipings = CustomerShiping::where('address_name', 'LIKE', "%$query%")
                                                        ->orWhere('full_address', 'LIKE', "%$query%")
                                                        ->orWhere(function (Builder $q) use ($query) {
                                                            $phone = preg_replace('/[^0-9]/', '', $query);
                                                            if (trim($phone)) $q->where('phone', 'LIKE', "%$phone%");
                                                        })
                                                        ->limit(10)
                                                        ->get();

                                                    return $customer_shipings->mapWithKeys(function ($customer_shiping) {
                                                        return [$customer_shiping->getKey() => static::getCleanOptionString($customer_shiping)];
                                                    })->toArray();
                                                }
                                                return [];
                                            })
                                            ->afterStateUpdated(function ($state, $set) {
                                                if (!blank($state)) {
                                                    $customer_shiping = CustomerShiping::find($state);
                                                    if ($customer_shiping instanceof CustomerShiping) {
                                                        $customer = $customer_shiping->customer;
                                                        $set('customer_id', $customer->id);
                                                        $set('customer_shiping', $customer_shiping->id);
                                                    }
                                                }
                                            })


                                            ->columnSpan('full'),


                                    ]),



                                Select::make('customer_id')
                                    ->label(__('fields.order.customer'))
                                    ->helperText('ПОИСК В СПРАВОЧНИКАX')
                                    ->placeholder('Поиск по телефону\адресу доставки или названию точки')
                                    ->required()
                                    ->reactive()

                                    ->searchable()
                                    ->searchDebounce(1000)
                                    ->allowHtml()

                                    ->getSearchResultsUsing(function ($query) {
                                        if (strlen($query) >= 3) {

                                            $customer_shipings = Customer::where('name', 'like', "%$query%")
                                                ->whereHas('shipings', function ($q) use ($query) {
                                                    $q->where('address_name', 'LIKE', "%$query%")
                                                        ->orWhere('full_address', 'LIKE', "%$query%")
                                                        ->orWhere(function (Builder $q) use ($query) {
                                                            $phone = preg_replace('/[^0-9]/', '', $query);
                                                            if (trim($phone)) $q->where('phone', 'LIKE', "%$phone%");
                                                        });
                                                })
                                                ->with(['shipings' => function ($q) use ($query) {
                                                    $q->where('address_name', 'LIKE', "%$query%")
                                                        ->orWhere('full_address', 'LIKE', "%$query%")
                                                        ->orWhere(function (Builder $q) use ($query) {
                                                            $phone = preg_replace('/[^0-9]/', '', $query);
                                                            if (trim($phone)) $q->where('phone', 'LIKE', "%$phone%");
                                                        });
                                                }])

                                                ->limit(10)
                                                ->get();

                                            return $customer_shipings->mapWithKeys(function ($customer_shiping) {
                                                return [$customer_shiping->getKey() => static::getCleanOptionString($customer_shiping)];
                                            })->toArray();
                                        }
                                        return [];
                                    })
                                    ->afterStateUpdated(function ($state, $set) {
                                        if (!blank($state)) {
                                            $customer_shiping = CustomerShiping::find($state);
                                            if ($customer_shiping instanceof CustomerShiping) {
                                                $customer = $customer_shiping->customer;
                                                $set('customer_id', $customer->id);
                                                $set('customer_shiping', $customer_shiping->id);
                                            }
                                        }
                                    })
                                    ->getOptionLabelUsing(function ($state) {
                                        return Customer::find($state)?->name;
                                    }),


                                Select::make('customer_shiping')
                                    ->label(__('customer_shiping'))
                                    ->helperText('ПОИСК В СПРАВОЧНИКАX')
                                    ->reactive()
                                    ->disabled(fn ($get) => !$get('customer_id'))
                                    ->required()
                                    ->options(function ($state, $get, $component) {
                                        if ($get('customer_id')) {
                                            $customer_shipings = CustomerShiping::where('customer_id', $get('customer_id'))->get();
                                            $data = $customer_shipings->mapWithKeys(fn ($c_ship) => [$c_ship->getKey() => $c_ship->full_address])->toArray();
                                            $data['custom_address'] = 'Указать другой адрес';

                                            return $data;
                                        }
                                    })
                                    ->disablePlaceholderSelection(),

                            ])
                            ->columns(2),

                    ])
                    ->hiddenOn('view'),

                // Section::make('Адрес доставки')
                //     ->schema([
                //         Grid::make()
                //             ->schema([
                //                 TextInput::make('full_address')
                //                     ->label(__('fields.shiping.full_address'))
                //                     ->disableAutocomplete()
                //                     ->maxLength(255)
                //                     ->columnSpan('full'),
                //             ]),
                //         Grid::make()
                //             ->schema([

                //                 TextInput::make('country')
                //                     ->label(__('fields.shiping.country'))
                //                     ->disableAutocomplete()
                //                     ->maxLength(255),
                //                 TextInput::make('region')
                //                     ->label(__('fields.shiping.region'))
                //                     ->disableAutocomplete()
                //                     ->maxLength(255),
                //                 TextInput::make('district')
                //                     ->label(__('fields.shiping.district'))
                //                     ->disableAutocomplete()
                //                     ->maxLength(255),
                //                 TextInput::make('locality')
                //                     ->label(__('fields.shiping.locality'))
                //                     ->disableAutocomplete()
                //                     ->maxLength(255),
                //                 TextInput::make('street')
                //                     ->label(__('fields.shiping.street'))
                //                     ->disableAutocomplete()
                //                     ->maxLength(255),
                //                 TextInput::make('house_number')
                //                     ->label(__('fields.shiping.house_number'))
                //                     ->disableAutocomplete()
                //                     ->maxLength(255),


                //                 TextInput::make('house_frontway')
                //                     ->label(__('fields.shiping.house_frontway'))
                //                     ->disableAutocomplete()
                //                     ->maxLength(255),

                //                 TextInput::make('house_floor')
                //                     ->label(__('fields.shiping.house_floor'))
                //                     ->disableAutocomplete()
                //                     ->maxLength(255),

                //                 TextInput::make('apartment')
                //                     ->label(__('fields.shiping.apartment'))
                //                     ->disableAutocomplete()
                //                     ->maxLength(255),

                //                 TextInput::make('intercom_code')
                //                     ->label(__('fields.shiping.intercom_code'))
                //                     ->disableAutocomplete()
                //                     ->maxLength(255),

                //                 Grid::make()
                //                     ->schema([
                //                         TextInput::make('latitude')
                //                             ->label(__('fields.shiping.latitude'))
                //                             ->disableAutocomplete()
                //                             ->hint(__('fields.shiping.latitude'))
                //                             ->hintIcon('heroicon-o-globe'),

                //                         TextInput::make('longitude')
                //                             ->label(__('fields.shiping.longitude'))
                //                             ->disableAutocomplete()
                //                             ->hint(__('fields.shiping.longitude'))
                //                             ->hintIcon('heroicon-o-globe'),

                //                     ]),


                //                 Hidden::make('type'),

                //             ])
                //             ->columns(2),

                //     ])
                //     ->collapsible(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_address')
                    ->label(__('fields.shiping.full_address'))
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->sortable(),
            ])
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
            'index' => Pages\ListOrders::route('/'),
            // 'create' => Pages\CreateOrder::route('/create'),
            // 'view' => Pages\ViewOrder::route('/{record}/view'),
            // 'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getCleanOptionString(Model $model): string
    {
        return Purify::clean(
            view('filament.components.select-shiping-address')
                ->with('full_address', $model?->full_address)
                ->with('address_name', $model?->address_name)
                ->with('phone', $model?->phone)
                ->with('customer_name', $model?->customer->name)
                ->render()
        );
    }
}
