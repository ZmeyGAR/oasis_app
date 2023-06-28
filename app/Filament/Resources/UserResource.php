<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\DatePicker;


use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Filament\Forms\Components\TextInput\Mask;
use STS\FilamentImpersonate\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static function getNavigationLabel(): string
    {
        return trans('filament-user::user.resource.label');
    }

    public static function getPluralLabel(): string
    {
        return trans('filament-user::user.resource.label');
    }

    public static function getLabel(): string
    {
        return trans('filament-user::user.resource.single');
    }

    protected static function getNavigationGroup(): ?string
    {
        // return config('filament-user.group');
        return __('filament-shield::filament-shield.nav.group');
    }

    protected function getTitle(): string
    {
        return trans('filament-user::user.resource.title.resource');
    }

    public static function form(Form $form): Form
    {
        $rows = [
            Group::make()
                ->schema([
                    Section::make(__('filament-user::user.section.general.title'))
                        ->label(__('filament-user::user.section.general.title'))
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->label(trans('filament-user::user.resource.name')),

                            TextInput::make('email')
                                ->email()
                                ->required()
                                ->label(trans('filament-user::user.resource.email')),

                            TextInput::make('phone')
                                ->disableAutocomplete()
                                ->label(__('filament-user::user.resource.phone'))
                                ->placeholder('+7 (***) *** - ** - **')
                                ->tel()
                                ->mask(fn (Mask $mask) => $mask->pattern('+{7} (000) 000-00-00'))
                                ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                ->maxValue(50),

                            Forms\Components\TextInput::make('password')
                                ->label(trans('filament-user::user.resource.password'))
                                ->password()
                                ->maxLength(255)
                                ->dehydrateStateUsing(static function ($state) use ($form) {
                                    if (!empty($state)) {
                                        return Hash::make($state);
                                    }
                                    $user = User::find($form->getColumns());
                                    if ($user) {
                                        return $user->password;
                                    }
                                }),
                        ]),
                ])
                ->columns(1)
                ->columnSpan(['lg' => fn () => !config('filament-user.shield') ? 'full' : 1]),

            Group::make()
                ->schema([
                    Section::make(__('filament-user::user.section.roles.title'))
                        ->label(__('filament-user::user.section.roles.title'))
                        ->schema([
                            Select::make('roles')->relationship('roles', 'name')
                                ->multiple()
                                ->label(trans('filament-user::user.resource.roles')),
                        ]),
                ])
                ->columnSpan(['lg' => 1])
                ->hidden(fn () => !config('filament-user.shield')),

            Tabs::make('User Details Info')
                ->tabs([
                    Tabs\Tab::make(__('filament-user::user.section.address_resident.title'))
                        ->schema([
                            Grid::make()
                                ->relationship('address_resident')
                                ->schema([

                                    Select::make('address_resident_geocoding')
                                        ->label(__('fields.shiping.search_address'))
                                        ->helperText(__('fields.shiping.search_address_description'))
                                        ->searchable()
                                        ->reactive()
                                        ->dehydrated(false)
                                        ->searchDebounce(2000)
                                        ->disablePlaceholderSelection()
                                        ->hiddenOn('view')
                                        ->columnSpan('full')
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
                                        ->afterStateUpdated(function ($state, $set, $get) {
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
                        ]),
                    Tabs\Tab::make(__('filament-user::user.section.address_place.title'))
                        ->schema([
                            Grid::make()
                                ->relationship('address_place')
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
                                        ->columnSpan('full')
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
                        ]),
                    Tabs\Tab::make(__('filament-user::user.section.passport.title'))
                        ->schema([
                            Grid::make()
                                ->relationship('passport')
                                ->schema([
                                    TextInput::make('number')
                                        ->label(trans('filament-user::user.section.passport.number')),
                                    TextInput::make('granted')
                                        ->label(trans('filament-user::user.section.passport.granted')),
                                    DatePicker::make('granted_at')
                                        ->label(trans('filament-user::user.section.passport.granted_at')),
                                ]),
                        ]),
                ])
                ->columnSpan('full'),

        ];

        // if (config('filament-user.shield')) {
        //     $rows[] = Forms\Components\Select::make('roles')->relationship('roles', 'name')
        //         ->multiple()
        //         ->label(trans('filament-user::user.resource.roles'));
        // }

        $form->schema($rows);

        return $form;
    }

    public static function table(Table $table): Table
    {
        $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->label(trans('filament-user::user.resource.id'))
                    ->toggleable(),
                TextColumn::make('name')
                    ->sortable()->searchable()
                    ->label(trans('filament-user::user.resource.name')),
                TextColumn::make('email')
                    ->sortable()
                    ->searchable()
                    ->label(trans('filament-user::user.resource.email')),

                Tables\Columns\TagsColumn::make('roles.name')
                    ->label(trans('filament-user::user.resource.roles')),


                // BooleanColumn::make('email_verified_at')->sortable()->searchable()->label(trans('filament-user::user.resource.email_verified_at')),
                Tables\Columns\TextColumn::make('created_at')->label(trans('filament-user::user.resource.created_at'))
                    ->dateTime('M j, Y')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label(trans('filament-user::user.resource.updated_at'))
                    ->dateTime('M j, Y')
                    ->toggleable()
                    ->sortable(),

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->filters([
                // Tables\Filters\Filter::make('verified')
                //     ->label(trans('filament-user::user.resource.verified'))
                //     ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                // Tables\Filters\Filter::make('unverified')
                //     ->label(trans('filament-user::user.resource.unverified'))
                //     ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
            ]);

        if (config('filament-user.impersonate')) {
            $table->prependActions([
                Impersonate::make('impersonate'),
            ]);
        }

        return $table;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/view/{record}'),
        ];
    }
}
