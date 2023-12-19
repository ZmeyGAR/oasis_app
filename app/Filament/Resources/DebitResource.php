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
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;

class DebitResource extends Resource
{
    protected static ?string $model = Debit::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

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
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
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
