<?php

namespace App\Filament\Resources;

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

                Tabs::make('Shiping address')
                    ->tabs([
                        Tab::make(__('fields.shiping.tab.address'))
                            ->schema([

                                Section::make('Укажите адрес доставки, используя справочники или укажите новый адрес доставки')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                Select::make('customer_shiping')
                                                    ->label(__('fields.order.customer_shiping'))
                                                    ->searchable()
                                                    ->allowHtml()
                                                    ->searchDebounce(1000)
                                                    ->helperText('ПОИСК В СПРАВОЧНИКАX')
                                                    ->placeholder('Поиск телефона\адреса доставки или название точки')
                                                    ->getSearchResultsUsing(function ($query) {
                                                        if (strlen($query) >= 5) {

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
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, $set) {

                                                        $ship_address = CustomerShiping::find($state);
                                                        if ($ship_address) {
                                                            $set('geocoding', '');
                                                            // $set('latLong', $state->latitude . ', ' . $state->longitude);
                                                            $set('full_address', $ship_address->full_address);
                                                            $set('country', $ship_address->country);
                                                            $set('region', $ship_address->region);
                                                            $set('district', $ship_address->district);
                                                            $set('locality', $ship_address->locality);
                                                            $set('street', $ship_address->street);
                                                            $set('house_number', $ship_address->houseNumber);
                                                            $set('latitude', $ship_address->latitude);
                                                            $set('longitude', $ship_address->longitude);
                                                            $set('type', $ship_address->type);
                                                        } else {
                                                            // $set('latLong', '');
                                                            $set('full_address', '');
                                                            $set('country', '');
                                                            $set('region', '');
                                                            $set('district', '');
                                                            $set('locality', '');
                                                            $set('street', '');
                                                            $set('house_number', '');
                                                            $set('latitude', '');
                                                            $set('longitude', '');
                                                            $set('type', '');
                                                        }
                                                    }),


                                                Select::make('geocoding')
                                                    ->label(__('fields.shiping.search_address'))
                                                    ->helperText('УКАЗАТЬ НОВЫЙ АДРЕС')
                                                    ->searchable()
                                                    ->reactive()
                                                    ->dehydrated(false)
                                                    ->searchDebounce(2000)
                                                    ->disablePlaceholderSelection()
                                                    ->getOptionLabelUsing(function ($value) {
                                                        if ($value) {
                                                            return $value;
                                                        }
                                                    })
                                                    ->getSearchResultsUsing(function ($query) {
                                                        if (strlen($query) >= 5) {
                                                            $results = [];
                                                            foreach (YaGeo($query) as $result) {
                                                                $results[json_encode($result, JSON_UNESCAPED_UNICODE)] = $result->full_address;
                                                            }
                                                            return $results;
                                                        }
                                                        return [];
                                                    })
                                                    ->afterStateUpdated(function ($state, $set) {

                                                        if (is_valid_json($state)) {
                                                            $state = json_decode($state);

                                                            $set('customer_shiping_id', '');

                                                            // $set('latLong', $state->latitude . ', ' . $state->longitude);
                                                            $set('full_address', $state->full_address);
                                                            $set('country', $state->country);
                                                            $set('region', $state->region);
                                                            $set('district', $state->district);
                                                            $set('locality', $state->locality);
                                                            $set('street', $state->street);
                                                            $set('house_number', $state->houseNumber);
                                                            $set('latitude', $state->latitude);
                                                            $set('longitude', $state->longitude);
                                                            $set('type', $state->type);
                                                        } else {
                                                            // $set('latLong', '');
                                                            $set('full_address', '');
                                                            $set('country', '');
                                                            $set('region', '');
                                                            $set('district', '');
                                                            $set('locality', '');
                                                            $set('street', '');
                                                            $set('house_number', '');
                                                            $set('latitude', '');
                                                            $set('longitude', '');
                                                            $set('type', '');
                                                        }
                                                    }),

                                            ])
                                            ->columns(2),

                                    ])
                                    ->hiddenOn('view'),

                                Section::make('Адрес доставки')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextInput::make('full_address')
                                                    ->label(__('fields.shiping.full_address'))
                                                    ->disableAutocomplete()
                                                    ->maxLength(255)
                                                    ->columnSpan('full'),
                                            ]),
                                        Grid::make()
                                            ->schema([

                                                TextInput::make('country')
                                                    ->label(__('fields.shiping.country'))
                                                    ->disableAutocomplete()
                                                    ->maxLength(255),
                                                TextInput::make('region')
                                                    ->label(__('fields.shiping.region'))
                                                    ->disableAutocomplete()
                                                    ->maxLength(255),
                                                TextInput::make('district')
                                                    ->label(__('fields.shiping.district'))
                                                    ->disableAutocomplete()
                                                    ->maxLength(255),
                                                TextInput::make('locality')
                                                    ->label(__('fields.shiping.locality'))
                                                    ->disableAutocomplete()
                                                    ->maxLength(255),
                                                TextInput::make('street')
                                                    ->label(__('fields.shiping.street'))
                                                    ->disableAutocomplete()
                                                    ->maxLength(255),
                                                TextInput::make('house_number')
                                                    ->label(__('fields.shiping.house_number'))
                                                    ->disableAutocomplete()
                                                    ->maxLength(255),


                                                TextInput::make('house_frontway')
                                                    ->label(__('fields.shiping.house_frontway'))
                                                    ->disableAutocomplete()
                                                    ->maxLength(255),

                                                TextInput::make('house_floor')
                                                    ->label(__('fields.shiping.house_floor'))
                                                    ->disableAutocomplete()
                                                    ->maxLength(255),

                                                TextInput::make('apartment')
                                                    ->label(__('fields.shiping.apartment'))
                                                    ->disableAutocomplete()
                                                    ->maxLength(255),

                                                TextInput::make('intercom_code')
                                                    ->label(__('fields.shiping.intercom_code'))
                                                    ->disableAutocomplete()
                                                    ->maxLength(255),

                                                Grid::make()
                                                    ->schema([
                                                        TextInput::make('latitude')
                                                            ->label(__('fields.shiping.latitude'))
                                                            ->disableAutocomplete()
                                                            ->hint(__('fields.shiping.latitude'))
                                                            ->hintIcon('heroicon-o-globe'),

                                                        TextInput::make('longitude')
                                                            ->label(__('fields.shiping.longitude'))
                                                            ->disableAutocomplete()
                                                            ->hint(__('fields.shiping.longitude'))
                                                            ->hintIcon('heroicon-o-globe'),

                                                    ]),


                                                Hidden::make('type'),

                                            ])
                                            ->columns(2),

                                    ])
                                    ->collapsible(),





                            ]),
                    ])->columnSpan('full'),

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
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}/view'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
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
