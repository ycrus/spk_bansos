<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramResource\Pages;
use App\Filament\Resources\ProgramResource\RelationManagers;
use App\Filament\Resources\ProgramResource\RelationManagers\CriteriaRelationManager;
use App\Models\Criteria;
use App\Models\Program;
use Filament\Actions\Action;
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
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Program')
                        ->schema([
                            TextInput::make('name')
                        ]),


                ]),
                Group::make()->schema([
                    Section::make()
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Placeholder::make("total_criteria")
                                        ->label("Total Criteria")
                                        ->content(function ($get) {
                                            return collect($get('criteria_id'))
                                                ->pluck('criteria_id')
                                                ->count();
                                        }),
                                    Placeholder::make('Total Criteria Weight')
                                        ->label("Total Criteria")
                                        ->content(function ($get) {
                                            return collect($get('weight'))
                                                ->sum($get('weight'));
                                        })
                                ])
                        ]),
                ]),
                Section::make()->schema([
                    Repeater::make("criteriaProgram")
                        ->relationship()
                        ->schema([
                            Select::make('criteria_id')
                                ->columnSpan(2)
                                ->options(Criteria::query()->pluck('title', 'id'))
                                ->required(),

                            TextInput::make('weight')
                                ->numeric()
                                ->columnSpan(1)
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    // $get('bobot', 'weight');
                                    $set('bobot', 'weight');
                                })
                                ->suffix('%')
                                ->maxValue(Program::maxCriteria())
                                ->default(0),
                        ])
                        // ->extraItemActions([
                        //     Action::make('ceck')
                        //         ->before(function (Action $action) {
                        //             if (! $this->record->is_passed) {
                        //                 Notification::make()
                        //                     ->title('This user quiz request must be passed to create meeting !')
                        //                     ->error()
                        //                     ->send();

                        //                 $action->halt();
                        //             }
                        //         })
                        //         ->icon('heroicon-m-envelope')
                        //         ->action(function (array $arguments, Repeater $component): void {
                        //             $itemData = $component->getItemState($arguments['weight']);
                        //         }),
                        // ])
                        ->orderColumn('id')
                        ->grid(2)
                        ->columns([
                            'default' => 3,
                            'sm' => 3,
                            'md' => 3,
                            'lg' => 3
                        ])->reactive(),
                ])

            ]);
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
