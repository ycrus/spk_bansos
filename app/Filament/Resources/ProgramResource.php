<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramResource\Pages;
use App\Filament\Resources\ProgramResource\RelationManagers\CriteriaRelationManager;
use App\Models\Criteria;
use App\Models\Program;
use Filament\Forms\Set;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Program')
                        ->schema([
                            TextInput::make('name'),
                        ]),
                ]),
                Group::make()->schema([
                    Section::make()
                        ->schema([
                            Grid::make(2)->schema([
                                Placeholder::make('totalweight')
                                    ->label('Total Weight')
                                    ->content(function (Get $get) {
                                        $total = self::calculateTotalWeight($get('criteriaProgram'));
                                        return $total . '%';
                                    }),
                            ]),
                        ]),
                ]),
                Section::make('Criteria Program')->schema([
                    Repeater::make('criteriaProgram')
                        ->relationship('criteriaProgram')
                        ->schema([
                            Select::make('criteria_id')
                                ->label('Criteria')
                                ->columnSpan(2)
                                ->options(Criteria::query()->pluck('title', 'id'))
                                ->required()
                                ->rules(['distinct'])
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            TextInput::make('weight')
                                ->numeric()
                                ->columnSpan(1)
                                ->rules(['required', 'min:1', 'max:100'])
                                ->suffix('%')
                                ->default(0),
                        ])
                        ->rules([new \App\Rules\CriteriaWeightRule()])
                        ->minItems(4)
                        ->live()
                        ->grid(2)
                        ->columns([
                            'default' => 3,
                            'sm' => 3,
                            'md' => 3,
                            'lg' => 3,
                        ]),
                ]),
            ]);
    }


    private static function calculateTotalWeight(?array $criteriaProgram): float
    {
        $items = $criteriaProgram ?? [];
        return array_sum(array_map(fn($item) => (float) ($item['weight'] ?? 0), $items));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
            ])
            ->filters([
                TrashedFilter::make(),
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // CriteriaRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrograms::route('/'),
            'create' => Pages\CreateProgram::route('/create'),
            'edit' => Pages\EditProgram::route('/{record}/edit'),
        ];
    }
}
