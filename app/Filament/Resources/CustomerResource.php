<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'customers';

    protected static ?string $slug = 'shop/customers';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';




    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Card::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('fields.customer.name'))
                            ->maxValue(50)
                            ->autofocus()
                            ->required(),

                        TextInput::make('email')
                            ->label(__('fields.customer.email'))
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true),

                        TextInput::make('phone')
                            ->label(__('fields.customer.phone'))
                            ->placeholder('+7 (***) *** - ** - **')
                            ->tel()
                            ->mask(fn (TextInput\Mask $mask) => $mask->pattern('+{7} (000) 000-00-00'))
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                            ->maxValue(50),

                        Select::make('person_type')
                            ->label(__('fields.customer.person_type'))
                            ->options(['LEGAL_PERSON' => 'Юр. лицо', 'INDIVIDUAL_PERSON' => 'Физ. лицо',])
                            ->reactive(),

                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => fn (?Customer $record) => $record === null ? 2 : 2]),

                Group::make()
                    ->schema([
                        Section::make(__('payment.section.title'))
                            ->label('Платежи')
                            ->schema([
                                Select::make('payments')
                                    ->multiple()
                                    ->label('Как платит клиент')
                                    ->relationship('payments', 'name')
                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => __("payment.method.$record->name"))
                                    ->preload(),


                                // Placeholder::make('created_at')
                                //     ->label(__('fields.customer.created_at'))
                                //     ->content(fn (Customer $record): ?string => $record->created_at?->diffForHumans()),

                                // Placeholder::make('updated_at')
                                //     ->label(__('fields.customer.updated_at'))
                                //     ->content(fn (Customer $record): ?string => $record->updated_at?->diffForHumans()),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1])
                // ->hidden(fn (?Customer $record) => $record === null)
                ,

                // Card::make()
                //     ->schema([
                //         Placeholder::make('created_at')
                //             ->label(__('fields.customer.created_at'))
                //             ->content(fn (Customer $record): ?string => $record->created_at?->diffForHumans()),

                //         Placeholder::make('updated_at')
                //             ->label(__('fields.customer.updated_at'))
                //             ->content(fn (Customer $record): ?string => $record->updated_at?->diffForHumans()),
                //     ])
                //     ->columnSpan(['lg' => 1])
                //     ->hidden(fn (?Customer $record) => $record === null),


                Section::make(__('fields.section.heading.detail'))
                    ->schema([

                        Grid::make([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 1,
                            'lg' => 2,
                            'xl' => 2,
                            '2xl' => 2,
                        ])
                            ->schema([
                                Repeater::make('details')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('fields.detail.name'))
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('ownership')
                                            ->label(__('fields.detail.ownership'))
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('BIN_IIN')
                                            ->label(__('fields.detail.BIN_IIN'))
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('bank_account')
                                            ->label(__('fields.detail.bank_account'))
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('legal_address')
                                            ->label(__('fields.detail.legal_address'))
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('KBE')
                                            ->label(__('fields.detail.KBE'))
                                            ->required()
                                            ->maxLength(255),
                                    ])
                                    ->label('')
                                    ->itemLabel(function (array $state): ?string {
                                        return $state['name'] ?
                                            $state['name']
                                            :
                                            __('fields.forms.fieldset.detail.label');
                                    })
                                    ->createItemButtonLabel(__('fields.forms.fieldset.detail.addButton'))
                                    ->columnSpan([
                                        'default' => 1,
                                        'sm' => 1,
                                        'md' => 1,
                                        'lg' => 2,
                                        'xl' => 2,
                                        '2xl' => 2,
                                    ])
                                    ->columns(2)
                                    ->collapsible()
                                    ->collapsed()
                                    ->cloneable()
                            ])
                    ])
                    ->hidden(fn (Closure $get) => $get('person_type') === 'INDIVIDUAL_PERSON'),



            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('fields.customer.name'))
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('fields.customer.email'))
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('fields.customer.phone'))
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('person_type')
                    ->label('Юр/Физ лицо')
                    ->formatStateUsing(function ($state) {
                        $person_types = [
                            'LEGAL_PERSON' => 'Юр.лицо',
                            'INDIVIDUAL_PERSON' => 'Физ.лицо',
                        ];
                        return $person_types[$state];
                    })
                    ->sortable()
                    ->toggleable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\CustomerDetailRelationManager::class,
            RelationManagers\ShipingsRelationManager::class,
            // RelationManagers\ShipingsRelationManager::class,

        ];
    }

    public function hasCombinedRelationManagerTabsWithForm(): bool
    {
        return true;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'view' => Pages\ViewCustomer::route('/view/{record}'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament.pages.customer.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.pages.customer.plural_label');
    }


    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.customer.plural_label');
    }

    protected static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.shop.label');
    }
}
