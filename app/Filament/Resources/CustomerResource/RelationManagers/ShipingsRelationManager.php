<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Models\CustomerShiping;
use Filament\Forms;
use Filament\Forms\Components\Actions\Modal\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Collection;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ShipingsRelationManager extends RelationManager
{
    protected static string $relationship = 'shipings';

    protected static ?string $recordTitleAttribute = 'address_name';

    protected static ?string $title = 'title_custom_shipings';

    public static function getTitle(): string
    {
        return __('filament.pages.shiping.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Toggle::make('isMain')
                    ->label(__('fields.shiping.isMain')),

                TextInput::make('address_name')
                    ->required()
                    ->label(__('fields.shiping.address_name'))
                    ->maxLength(255),

                TextInput::make('firstname')
                    ->required()
                    ->label(__('fields.shiping.firstname'))
                    ->maxLength(255),

                TextInput::make('lastname')
                    ->required()
                    ->label(__('fields.shiping.lastname'))
                    ->maxLength(255),

                TextInput::make('email')
                    ->label(__('fields.shiping.email'))
                    ->required()
                    ->email(),

                TextInput::make('phone')
                    ->label(__('fields.shiping.phone'))
                    ->placeholder('+7 (***) *** - ** - **')
                    ->tel()
                    ->mask(fn (Mask $mask) => $mask->pattern('+{7} (000) 000-00-00'))
                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                    ->maxValue(50),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ToggleColumn::make('isMain')
                    ->label(__('fields.shiping.isMain'))
                    ->disabled(),

                TextColumn::make('address_name')
                    ->label(__('fields.shiping.address_name')),
                TextColumn::make('firstname')
                    ->label(__('fields.shiping.firstname')),
                TextColumn::make('lastname')
                    ->label(__('fields.shiping.lastname')),
                TextColumn::make('email')
                    ->label(__('fields.shiping.email')),
                TextColumn::make('phone')
                    ->label(__('fields.shiping.phone')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
