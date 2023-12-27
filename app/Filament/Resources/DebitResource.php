<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DebitResource\Pages;
use App\Filament\Resources\DebitResource\RelationManagers;
use App\Filament\Resources\DebitResource\RelationManagers\ContractServicesRelationManager;
use App\Models\Contract;
use App\Models\Debit;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Webbingbrasil\FilamentDateFilter\DateFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Layout;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\DB;

class DebitResource extends Resource
{
    protected static ?string $model = Debit::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 4;
    public static function form(Form $form): Form
    {

        return $form
            ->schema([

                Placeholder::make('period')
                    ->label(__('fields.debit.period'))
                    ->content(function ($get) {
                        return Str::ucfirst(Carbon::createFromDate($get('period'))->monthName) . ' ' . Carbon::createFromDate($get('period'))->year;
                    }),

                Placeholder::make('status')
                    ->label(__('fields.debit.status.label'))
                    ->content(function ($get) {
                        $statuses = [
                            'open'  => __('fields.debit.status.values.open'),
                            'close'  => __('fields.debit.status.values.close'),
                        ];
                        return $statuses[$get('status')];
                    })

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period')
                    ->label(__('fields.debit.period'))
                    ->formatStateUsing(fn ($state) => Str::ucfirst(Carbon::createFromDate($state)->monthName) . ' ' . Carbon::createFromDate($state)->year),

                TextColumn::make('status')
                    ->label(__('fields.debit.status.label'))
                    ->enum([
                        'open'  => __('fields.debit.status.values.open'),
                        'close'  => __('fields.debit.status.values.close'),
                    ]),

                TextColumn::make('count')
                    ->label(__('fields.debit.count'))
                    ->getStateUsing(
                        static function ($record) {
                            return $record->contract_services()->withPivot('count')->sum('contract_services_debit.count');
                        }
                    ),
                TextColumn::make('sum')
                    ->label(__('fields.debit.sum'))
                    ->getStateUsing(
                        static function ($record) {
                            return money($record->contract_services()->withPivot('sum')->sum('contract_services_debit.sum'), 'KZT', true);
                        }
                    ),

            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('period')
                    ->form([
                        Grid::make(4)
                            ->schema([
                                Fieldset::make()
                                    ->label(__('fields.debit.filter.period.from'))
                                    ->schema([
                                        Forms\Components\Select::make('from.month')
                                            ->label(__('fields.debit.filter.period.from_month'))
                                            ->options(function () {
                                                $months = Debit::select(DB::raw('MONTH(period) as month'))->distinct()->orderBy('month')->get();
                                                return $months->pluck('', 'month')->map(fn ($v, $k) => Str::ucfirst(Carbon::createFromFormat('m', $k)->monthName))->toArray();
                                            }),
                                        Forms\Components\Select::make('from.year')
                                            ->label(__('fields.debit.filter.period.from_year'))
                                            ->options(function () {
                                                $years = Debit::select(DB::raw('YEAR(period) as year'))->distinct()->orderBy('year')->get();
                                                return $years->pluck('year', 'year')->toArray();
                                            }),
                                    ])
                                    ->columnSpan(2),
                                Fieldset::make()
                                    ->label(__('fields.debit.filter.period.to'))
                                    ->schema([
                                        Forms\Components\Select::make('to.month')
                                            ->label(__('fields.debit.filter.period.to_month'))
                                            ->options(function () {
                                                $months = Debit::select(DB::raw('MONTH(period) as month'))
                                                    ->distinct()
                                                    ->orderBy('month')->get();
                                                return $months->pluck('', 'month')->map(fn ($v, $k) => Str::ucfirst(Carbon::createFromFormat('m', $k)->monthName))->toArray();
                                            }),
                                        Forms\Components\Select::make('to.year')
                                            ->label(__('fields.debit.filter.period.to_year'))
                                            ->options(function () {
                                                $years = Debit::select(DB::raw('YEAR(period) as year'))->distinct()->orderBy('year')->get();
                                                return $years->pluck('year', 'year')->toArray();
                                            }),
                                    ])->columnSpan(2),
                            ])->columns(4),

                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                ($data['from']['year'] and $data['from']['month']) ? Carbon::createFromDate($data['from']['year'], $data['from']['month'], 1)->format('Y-m-d') : null,
                                fn (Builder $query, $date): Builder => $query->whereDate('period', '>=', $date)
                            )
                            ->when(
                                ($data['to']['year'] and $data['to']['month']) ? Carbon::createFromDate($data['to']['year'], $data['to']['month'], 1)->format('Y-m-d') : null,
                                fn (Builder $query, $date): Builder => $query->whereDate('period', '<=', $date)
                            );
                    })
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('closePeriod')
                    ->label(__('filament.debit.period.close'))
                    ->button()
                    ->color('danger')
                    ->action(function ($record) {
                        $record->update(['status' => 'close']);
                    })
                    ->visible(function ($record) {
                        return $record->status === 'open';
                    })

            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ContractServicesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDebits::route('/'),
            'create' => Pages\CreateDebit::route('/create'),
            'edit' => Pages\EditDebit::route('/{record}/edit'),
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
    //     return __('filament.navigation.guide.label');
    // }
}
