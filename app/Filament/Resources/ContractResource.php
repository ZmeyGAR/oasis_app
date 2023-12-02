<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Filament\Resources\ContractResource\RelationManagers;
use App\Models\Client;
use App\Models\Contract;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->columns(2)->schema([

                    TextInput::make('number')
                        ->label(__('fields.contract.number'))
                        ->required(),

                    DateTimePicker::make('date')
                        ->label(__('fields.contract.date'))
                        ->required(),

                    Select::make('type')
                        ->label(__('fields.contract.type.label'))
                        ->options([
                            'local'     => __('fields.contract.type.values.local'),
                            'central'   => __('fields.contract.type.values.central'),
                        ])
                        ->required(),

                    Select::make('client_id')
                        ->label(__('fields.contract.client.name'))
                        ->options(fn () => Client::take(10)->get()->pluck('name', 'id'))
                        ->getSearchResultsUsing(fn (string $search) => Client::where('name', 'LIKE', '%' . $search .  '%')->limit(10)->pluck('name', 'id'))
                        ->getOptionLabelUsing(fn ($value): ?string => Client::find($value)?->name)
                        ->searchable()
                        ->searchDebounce(500)
                        ->required(),


                    DateTimePicker::make('date_start')
                        ->label(__('fields.contract.date_start'))
                        ->required(),

                    DateTimePicker::make('date_end')
                        ->label(__('fields.contract.date_end'))
                        ->required(),
                ]),

                Card::make()->schema([

                    Textarea::make('comment')
                        ->rows(3)
                        ->cols(20)
                        ->label(__('fields.contract.comment')),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label(__('fields.contract.number'))
                    ->searchable(),

                TextColumn::make('type')
                    ->label(__('fields.contract.type.label'))
                    ->enum([
                        'local'     => __('fields.contract.type.values.local'),
                        'central'   => __('fields.contract.type.values.central'),
                    ])
                    ->sortable(),

                TextColumn::make('client.name')
                    ->label(__('fields.contract.client.name')),
                TextColumn::make('date')
                    ->label(__('fields.contract.date')),
                TextColumn::make('date_start')
                    ->label(__('fields.contract.date_start')),
                TextColumn::make('date_end')
                    ->label(__('fields.contract.date_end')),


            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListContracts::route('/'),
            // 'create' => Pages\CreateContract::route('/create'),
            // 'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getModelLabel(): string
    {
        return __('filament.pages.contract.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.pages.contract.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.contract.plural_label');
    }

    protected static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.guide.label');
    }
}
