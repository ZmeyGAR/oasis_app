<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DistrictResource\Pages;
use App\Filament\Resources\DistrictResource\RelationManagers;
use App\Models\Area;
use App\Models\District;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('fields.district.name'))
                    ->maxValue(255)
                    ->autofocus()
                    ->required(),

                Hidden::make('state_id')
                    ->required(),

                Select::make('area_id')
                    ->label(__('fields.area.name'))
                    ->options(fn () => Area::take(10)->get()->pluck('name', 'id'))
                    ->getSearchResultsUsing(fn (string $search) => Area::where('name', 'LIKE', '%' . $search .  '%')->limit(10)->pluck('name', 'id'))
                    ->getOptionLabelUsing(fn ($value): ?string => Area::find($value)?->name)
                    ->searchable()
                    ->searchDebounce(500)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $record = Area::find($state);
                        if ($record) $set('state_id', $record->state->id);
                    })
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('fields.district.name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('state.name')->label(__('fields.state.name')),
                TextColumn::make('area.name')->label(__('fields.area.name')),
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
            'index' => Pages\ListDistricts::route('/'),
            // 'create' => Pages\CreateDistrict::route('/create'),
            // 'edit' => Pages\EditDistrict::route('/{record}/edit'),
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
        return __('filament.pages.district.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.pages.district.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.district.plural_label');
    }

    protected static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.state.label');
    }
}
