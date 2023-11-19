<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubServiceResource\Pages;
use App\Filament\Resources\SubServiceResource\RelationManagers;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\SubService;
use Filament\Forms;
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

class SubServiceResource extends Resource
{
    protected static ?string $model = SubService::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('fields.subservice.name'))
                    ->maxValue(255)
                    ->autofocus()
                    ->required(),

                Select::make('service_type_id')
                    ->label(__('fields.service_types.name'))
                    ->relationship('service_type', 'name')
                    ->options(ServiceType::all()->pluck('name', 'id')->toArray())
                    ->reactive()
                    ->required(),

                Select::make('service_id')
                    ->label(__('fields.service.name'))
                    ->options(function (callable $get) {
                        $service_type = ServiceType::find($get('service_type_id'));
                        if (!$service_type) return [];
                        return $service_type->services()->pluck('name', 'id')->toArray();
                    })
                    ->reactive()
                    ->disabled(fn (callable $get): bool => !$get('service_type_id'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('fields.subservice.name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('service_type.name')
                    ->label(__('fields.service_types.name')),

                TextColumn::make('service.name')
                    ->label(__('fields.service.name')),

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
            'index' => Pages\ListSubServices::route('/'),
            // 'create' => Pages\CreateSubService::route('/create'),
            // 'edit' => Pages\EditSubService::route('/{record}/edit'),
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
        return __('filament.pages.subservice.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.pages.subservice.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.subservice.plural_label');
    }

    protected static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.service_types.label');
    }
}
