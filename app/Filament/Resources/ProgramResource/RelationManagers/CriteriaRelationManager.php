<?php

namespace App\Filament\Resources\ProgramResource\RelationManagers;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CriteriaRelationManager extends RelationManager
{
    protected static string $relationship = 'criterias';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Placeholder::make("total_weight")
                        ->label("Total weight")
                        ->content(function ($get) {
                            return collect($get('weight'))
                                ->pluck('weight')
                                ->sum();
                        }),
                ]),
                Group::make()->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('weight')
                        ->suffix('%')
                        ->required(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('weight')
                    ->suffix('%'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
