<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DebitResource\Pages;
use App\Filament\Resources\DebitResource\RelationManagers;
use App\Models\Contract;
use App\Models\Debit;
use App\Models\Indicator;
use App\Models\Program;
use App\Models\ServiceType;
use App\Models\Station;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TagsColumn;

class DebitResource extends Resource
{
    protected static ?string $model = Debit::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Hidden::make('period')
                    ->default(function () {
                        return today()->month;
                    })
                    ->required(),

                Hidden::make('status'),

                Select::make('contract_id')
                    ->label(__('fields.debit.contract'))
                    ->options(fn () => Contract::take(10)->get()->pluck('number', 'id'))
                    ->getSearchResultsUsing(fn (string $search) => Contract::where('number', 'LIKE', '%' . $search .  '%')->limit(10)->pluck('number', 'id'))
                    ->getOptionLabelUsing(fn ($value): ?string => Contract::find($value)?->number)
                    ->searchable()
                    ->searchDebounce(500)
                    ->reactive()
                    ->required(),

                Select::make('activity_type')
                    ->label(__('fields.debit.activity_type.label'))
                    ->options([
                        'main'   => __('fields.debit.activity_type.values.main'),
                        'non-main'   => __('fields.debit.activity_type.values.non-main'),
                    ])
                    ->required(),

                Select::make('indicator_id')
                    ->label(__('fields.indicator.label'))
                    ->options(fn () => Indicator::take(10)->get()->pluck('name', 'id'))
                    ->getSearchResultsUsing(fn (string $search) => Indicator::where('name', 'LIKE', '%' . $search .  '%')->limit(10)->pluck('name', 'id'))
                    ->getOptionLabelUsing(fn ($value): ?string => Indicator::find($value)?->name)
                    ->searchable()
                    ->searchDebounce(500)
                    ->reactive()
                    ->required(),


                Select::make('program_id')
                    ->label(__('fields.program.label'))
                    ->options(fn () => Program::take(10)->get()->pluck('name', 'id'))
                    ->getSearchResultsUsing(fn (string $search) => Program::where('name', 'LIKE', '%' . $search .  '%')->limit(10)->pluck('name', 'id'))
                    ->getOptionLabelUsing(fn ($value): ?string => Program::find($value)?->name)
                    ->searchable()
                    ->searchDebounce(500)
                    ->reactive()
                    ->required(),

                TextInput::make('count')
                    ->label(__('fields.debit.count'))
                    ->numeric()
                    ->required(),

                Hidden::make('state_id')
                    ->required(),
                Hidden::make('area_id')
                    ->required(),
                Hidden::make('district_id')
                    ->required(),
                Hidden::make('city_id')
                    ->required(),

                Select::make('station_id')
                    ->label(__('fields.station.name'))
                    ->options(fn () => Station::take(10)->get()->pluck('name', 'id'))
                    ->getSearchResultsUsing(fn (string $search) => Station::where('name', 'LIKE', '%' . $search .  '%')->limit(10)->pluck('name', 'id'))
                    ->getOptionLabelUsing(fn ($value): ?string => Station::find($value)?->name)
                    ->searchable()
                    ->searchDebounce(500)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $record = Station::find($state);
                        if ($record) $set('state_id', $record->state->id);
                        if ($record) $set('area_id', $record->area->id);
                        if ($record) $set('district_id', $record->district->id);
                        if ($record) $set('city_id', $record->city->id);
                    })
                    ->required(),

                Select::make('services')
                    ->label(__('fields.debit.services'))
                    ->relationship('services', 'name')
                    ->multiple()
                    ->options(fn () => ServiceType::take(10)->get()->pluck('name', 'id'))
                    ->getSearchResultsUsing(fn (string $search) => ServiceType::where('name', 'LIKE', '%' . $search .  '%')->limit(10)->pluck('name', 'id'))
                    ->getOptionLabelUsing(fn ($value): ?string => ServiceType::find($value)?->name)
                    ->searchable()
                    ->searchDebounce(500)
                    ->reactive(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period')
                    ->label(__('fields.debit.period'))
                    ->enum([
                        1 => __('Январь'),
                        2 => __('Февраль'),
                        3 => __('Март'),
                        4 => __('Апрель'),
                        5 => __('Май'),
                        6 => __('Июнь'),
                        7 => __('Июль'),
                        8 => __('Август'),
                        9 => __('Сентябль'),
                        10 => __('Октябрь'),
                        11 => __('Ноябрь'),
                        12 => __('Декабрь'),
                    ])
                    ->sortable(),

                TextColumn::make('contract.number')->label(__('fields.debit.contract')),

                TextColumn::make('activity_type')
                    ->label(__('fields.debit.activity_type.label'))
                    ->enum([
                        'main'     => __('fields.debit.activity_type.values.main'),
                        'non-main'   => __('fields.debit.activity_type.values.non-main'),
                    ])
                    ->sortable(),

                TextColumn::make('station.name')->label(__('fields.debit.station')),

                TextColumn::make('count')->label(__('fields.debit.count')),
                TextColumn::make('indicator.name')->label(__('fields.debit.indicator')),

                TagsColumn::make('services.name')
                    ->label(__('fields.debit.services')),

                // TextColumn::make('status')
                //     ->label(__('fields.debit.status'))
                //     ->sortable()
                //     ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListDebits::route('/'),
            // 'create' => Pages\CreateDebit::route('/create'),
            // 'edit' => Pages\EditDebit::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament.pages.debit.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.pages.debit.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.debit.plural_label');
    }

    // protected static function getNavigationGroup(): ?string
    // {
    //     return __('filament.navigation.state.label');
    // }
}
