<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;


use App\Models\CustomerShiping;
use App\Models\ShipingAddressBalance;
use Filament\Forms;
use Filament\Forms\Components\Actions\Modal\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Collection;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Contracts\HasRelationshipTable;

use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;

class ShipingsRelationManager extends RelationManager
{
    protected static string $relationship = 'shipings';

    protected static ?string $recordTitleAttribute = 'address_name';

    protected static ?string $title = 'title_custom_shipings';

    public static function getTitle(): string
    {
        return __('filament.pages.shiping.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Tabs::make('Shiping address')
                    ->tabs([
                        Tab::make(__('fields.shiping.tab.general'))
                            ->schema([

                                Grid::make()
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('address_name')
                                            ->required()
                                            ->label(__('fields.shiping.address_name'))
                                            ->disableAutocomplete()
                                            ->maxLength(255),
                                        Toggle::make('isMain')
                                            ->inline(false)
                                            ->label(__('fields.shiping.isMain')),

                                        TextInput::make('firstname')
                                            ->required()
                                            ->disableAutocomplete()
                                            ->label(__('fields.shiping.firstname'))
                                            ->maxLength(255),

                                        TextInput::make('lastname')
                                            ->required()
                                            ->disableAutocomplete()
                                            ->label(__('fields.shiping.lastname'))
                                            ->maxLength(255),

                                        TextInput::make('email')
                                            ->disableAutocomplete()
                                            ->label(__('fields.shiping.email'))
                                            ->required()
                                            ->email(),

                                        TextInput::make('phone')
                                            ->disableAutocomplete()
                                            ->label(__('fields.shiping.phone'))
                                            ->placeholder('+7 (***) *** - ** - **')
                                            ->tel()
                                            ->mask(fn (Mask $mask) => $mask->pattern('+{7} (000) 000-00-00'))
                                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                            ->maxValue(50),

                                        RichEditor::make('comment')
                                            ->label(__('fields.shiping.comment'))
                                            ->columnSpan('full'),
                                    ]),


                            ]),
                        Tab::make(__('fields.shiping.tab.address'))
                            ->schema([

                                Select::make('geocoding')
                                    ->label(__('fields.shiping.search_address'))
                                    ->helperText(__('fields.shiping.search_address_description'))
                                    ->searchable()
                                    ->reactive()
                                    ->dehydrated(false)
                                    ->searchDebounce(2000)
                                    ->disablePlaceholderSelection()
                                    ->hiddenOn('view')
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

                                            $set('latLong', $state->latitude . ', ' . $state->longitude);
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
                                            $set('latLong', '');
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




                            ]),
                        Tab::make(__('fields.shiping.tab.work_time'))
                            ->schema([

                                Grid::make()
                                    ->relationship('work_time')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TimePicker::make('start_at')
                                                    ->label(__('fields.shiping.work_time.start_at')),
                                                TimePicker::make('end_at')
                                                    ->label(__('fields.shiping.work_time.end_at')),

                                            ]),
                                        Grid::make()
                                            ->schema([
                                                TimePicker::make('launch_start_at')
                                                    ->label(__('fields.shiping.work_time.launch_start_at')),
                                                TimePicker::make('launch_end_at')
                                                    ->label(__('fields.shiping.work_time.launch_end_at')),

                                            ]),

                                        Select::make('weekend_days')
                                            ->label(__('fields.shiping.work_time.weekend_days'))
                                            ->multiple()
                                            ->options(function () {
                                                return [
                                                    'monday' => __('week_days.monday'),
                                                    'tuesday' => __('week_days.tuesday'),
                                                    'wednesday' => __('week_days.wednesday'),
                                                    'thursday' => __('week_days.thursday'),
                                                    'friday' => __('week_days.friday'),
                                                    'saturday' => __('week_days.saturday'),
                                                    'sunday' => __('week_days.sunday'),
                                                ];
                                            })
                                            ->preload(),
                                    ])

                            ]),
                        Tab::make(__('fields.shiping.tab.cooler_and_tara'))
                            ->schema([

                                Grid::make()
                                    ->relationship('cooler')
                                    ->schema([
                                        Toggle::make('having')
                                            ->inline(false)
                                            ->label(__('fields.shiping.cooler.having')),
                                        TextInput::make('count')
                                            ->label(__('fields.shiping.cooler.count'))
                                            ->type('number')
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0),
                                    ]),
                                Grid::make()
                                    ->relationship('tara')
                                    ->schema([
                                        TextInput::make('count')
                                            ->label(__('fields.shiping.tara.count'))
                                            ->type('number')
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->columnSpan('full'),
                                    ]),



                            ]),
                        Tab::make(__('fields.shiping.tab.balance'))
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        Placeholder::make('shiping_balance')
                                            ->label(__('fields.shiping.balance.total_balance'))
                                            ->content(fn (CustomerShiping $record) => new HtmlString('<b>' . $record->balance()->sum('balance') . '</b>'))
                                            ->columnSpan(1),
                                    ])
                                    ->hidden(fn (?CustomerShiping $record) => $record === null),

                                TableRepeater::make('balance')
                                    ->label(__('fields.shiping.balance.label'))
                                    ->relationship()
                                    ->schema([
                                        Placeholder::make('created_at')
                                            ->label(__('fields.shiping.balance.created_at'))
                                            ->content(fn (?ShipingAddressBalance $record): ?string => $record !== null ? $record->created_at->isoFormat('D MMMM Y г. H:m:s') : '')
                                            ->columnSpan(1),

                                        TextInput::make('balance')
                                            ->label(__('fields.shiping.balance.balance'))
                                            ->numeric()
                                            ->default(0)
                                            ->columnSpan(1),

                                        TextArea::make('description')
                                            ->label(__('fields.shiping.balance.description'))
                                            ->rows(1)
                                            ->columnSpan(3),


                                    ])
                                    ->colStyles([
                                        'balance' => 'width: 150px;',
                                        'description' => 'width: auto',
                                        'created_at' => 'width: 150px;',
                                    ])
                                    ->collapsible()
                                    // ->disableItemCreation()
                                    // ->disableItemDeletion()
                                    ->disableItemMovement()
                                    ->columnSpan('full'),

                            ]),


                    ])->columnSpan('full'),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ToggleColumn::make('isMain')
                    ->label(__('fields.shiping.isMain'))
                    ->disabled(),

                TextColumn::make('address_name')
                    ->label(__('fields.shiping.address_name')),
                TextColumn::make('firstname')
                    ->label(__('fields.shiping.firstname')),
                TextColumn::make('lastname')
                    ->label(__('fields.shiping.lastname')),
                TextColumn::make('email')
                    ->label(__('fields.shiping.email')),
                TextColumn::make('phone')
                    ->label(__('fields.shiping.phone')),
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
