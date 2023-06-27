<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TalonsRelationManager extends RelationManager
{
    protected static string $relationship = 'talons';

    protected static ?string $recordTitleAttribute = 'id';

    // protected $listeners = ['refresh' => '$refresh'];

    public static function getTitle(): string
    {
        return __('filament.pages.talons.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('balance')
                    ->label(__('fields.talons.balance'))
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->maxLength(255)
                    ->columnSpan('full'),

                Forms\Components\TextArea::make('description')
                    ->label(__('fields.talons.description'))
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.talons.created_at'))
                    ->dateTime(),
                Tables\Columns\TextColumn::make('balance')
                    ->label(__('fields.talons.balance')),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('fields.talons.description')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function (RelationManager $livewire) {
                        $livewire->emit('refresh');
                    })
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->after(function (RelationManager $livewire) {
                            $livewire->emit('refresh');
                        }),
                    DeleteAction::make()
                        ->after(function (RelationManager $livewire) {
                            $livewire->emit('refresh');
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
