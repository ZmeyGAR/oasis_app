<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoResource\Widgets\ServiceTypesWidget;
use App\Filament\Resources\ServiceTypeResource\Pages;
use App\Filament\Resources\ServiceTypeResource\RelationManagers;
use App\Models\ServiceType;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Component;

class ServiceTypeResource extends Resource
{
    protected static ?string $model = ServiceType::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('fields.service_types.name'))
                    ->maxValue(255)
                    ->autofocus()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('fields.service_types.name'))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()->after(function (Component $livewire) {
                        $livewire->emit('updateServiceTypesWidget');
                    }),
                    Tables\Actions\DeleteAction::make()->after(function (Component $livewire) {
                        $livewire->emit('updateServiceTypesWidget');
                    }),
                    Tables\Actions\RestoreAction::make()->after(function (Component $livewire) {
                        $livewire->emit('updateServiceTypesWidget');
                    }),
                    Tables\Actions\ForceDeleteAction::make()->after(function (Component $livewire) {
                        $livewire->emit('updateServiceTypesWidget');
                    }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->after(function (Component $livewire) {
                    $livewire->emit('updateServiceTypesWidget');
                }),
                Tables\Actions\ForceDeleteBulkAction::make()->after(function (Component $livewire) {
                    $livewire->emit('updateServiceTypesWidget');
                }),
                Tables\Actions\RestoreBulkAction::make()->after(function (Component $livewire) {
                    $livewire->emit('updateServiceTypesWidget');
                }),
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
            'index' => Pages\ListServiceTypes::route('/'),
            // 'create' => Pages\CreateServiceType::route('/create'),
            // 'edit' => Pages\EditServiceType::route('/{record}/edit'),
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
        return __('filament.pages.service_types.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.pages.service_types.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.service_types.plural_label');
    }

    protected static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.service_types.label');
    }
    public static function getWidgets(): array
    {
        return [
            ServiceTypesWidget::class,
        ];
    }
}
