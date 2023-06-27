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
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'customers';

    protected static ?string $slug = 'shop/customers';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected $listeners = ['refresh' => '$refresh'];


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Card::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('fields.customer.name'))
                            ->maxValue(255)
                            ->autofocus()
                            ->required(),

                        TextInput::make('email')
                            ->label(__('fields.customer.email'))
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true),

                        Select::make('person_type')
                            ->label(__('fields.customer.person_type'))
                            ->options(['LEGAL_PERSON' => 'Юр. лицо', 'INDIVIDUAL_PERSON' => 'Физ. лицо',])
                            ->required()
                            ->reactive(),

                        TextInput::make('phone')
                            ->label(__('fields.customer.phone'))
                            ->placeholder('+7 (***) *** - ** - **')
                            ->tel()
                            ->mask(fn (TextInput\Mask $mask) => $mask->pattern('+{7} (000) 000-00-00'))
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),


                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => fn (?Customer $record) => $record === null ? 2 : 2]),

                Group::make()
                    ->schema([
                        Section::make(__('payment.section.title'))
                            ->label(__('payment.section.title'))
                            ->schema([
                                Select::make('payments')
                                    ->multiple()
                                    ->label('Как платит клиент')
                                    ->relationship('payments', 'name')
                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => __("payment.method.$record->name"))
                                    ->preload(),
                            ]),

                        Section::make(__('fields.talons.section.title'))
                            ->label(__('fields.talons.section.title'))
                            ->schema([

                                Placeholder::make('total_balance')
                                    ->label(__('fields.talons.total_balance'))
                                    ->content(function (?Model $record) {
                                        return !is_null($record) ? new HtmlString("<b>{$record->refresh()->talons()->sum('balance')}</b>") : new HtmlString("<b>0</b>");
                                    })
                            ])
                            ->hiddenOn('create'),
                    ])
                    ->columnSpan(['lg' => 1]),

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
                    ->hidden(fn (Closure $get) => $get('person_type') !== 'LEGAL_PERSON'),



            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('fields.customer.name'))
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->sortable(),

                TextColumn::make('person_type')
                    ->label('Юр/Физ лицо')
                    ->formatStateUsing(function ($state) {
                        $person_types = [
                            'LEGAL_PERSON' => 'Юр.лицо',
                            'INDIVIDUAL_PERSON' => 'Физ.лицо',
                        ];
                        return $person_types[$state];
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->label(__('fields.customer.email'))
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->icon('heroicon-o-mail')
                    ->sortable(),

                TextColumn::make('phone')
                    ->label(__('fields.customer.phone'))
                    ->formatStateUsing(function ($state) {
                        return preg_replace(
                            '/^(\d)(\d{3})(\d{3})(\d{2})(\d{2})$/',
                            '+\1 (\2) \3-\4-\5',
                            (string)$state
                        );
                    })
                    ->icon('heroicon-o-phone')
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ShipingsRelationManager::class,
            RelationManagers\IndividualPriceRelationManager::class,
            RelationManagers\TalonsRelationManager::class,
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
