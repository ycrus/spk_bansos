<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramResource\Pages;
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
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-c-cube';

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
                            Grid::make(2)
                                ->schema([

                                    Placeholder::make('totalweight')
                                        ->label('Total Weight')
                                        ->content(function (Get $get) {
                                            $criteriaProgram = $get('criteriaProgram') ?? [];
                                            $weights = array_map(fn($item) => (float) ($item['weight'] ?? 0), $criteriaProgram);

                                            // Total weight
                                            return array_sum($weights) . '%';
                                        }),
                                ])
                        ]),
                ]),
                Section::make('Criteria Program')->schema([
                    Placeholder::make('error')
                        ->content(function () {
                            return session('error') ? session('error')->first('criteriaProgram') : '';
                        })
                        ->visible(fn() => session('error') && session('error')->has('criteriaProgram'))
                        ->label(''),
                    Repeater::make("criteriaProgram")
                        ->relationship()
                        ->schema([
                            Select::make('criteria_id')
                                ->label('Criteria')
                                ->columnSpan(2)
                                ->options(Criteria::query()->pluck('title', 'id'))
                                ->required()
                                ->rules([
                                    'distinct'
                                ])
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            TextInput::make('weight')
                                ->numeric()
                                ->columnSpan(1)
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::updateTotals($get, $set);
                                })
                                ->rules(['required', 'min:0', 'max:100']) // Validasi weight
                                ->suffix('%')
                                ->default(0),
                        ])
                        ->minItems(4)
                        ->live()
                        ->orderColumn('id')
                        ->grid(2)
                        ->columns([
                            'default' => 3,
                            'sm' => 3,
                            'md' => 3,
                            'lg' => 3
                        ]),
                ])

            ]);
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        // $selectedProducts = collect($get('criteriaProgram'));
        // Hitung total weight dari semua item
        $items = $get('criteriaProgram') ?? [];
        // Konversi setiap nilai weight menjadi numerik
        $weights = array_map(fn($weight) => (float) $weight, array_column($items, 'weight'));

        // Hitung total weight
        $totalWeight = array_sum($weights);

        if ($totalWeight != 100) {
            $set('error', 'The total weight must not exceed 100%.');
        } else {
            $set('error', null); // Reset error jika validasi lolos
        }

        // Lakukan sesuatu dengan total weight (opsional)
        $set('totalweight', $totalWeight);
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

    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        // Validasi total weight sebelum data disimpan
        self::validateTotalWeight($data);

        return $data;
    }

    protected static function mutateFormDataBeforeSave(array $data): array
    {
        // Validasi total weight sebelum data diperbarui
        self::validateTotalWeight($data);

        return $data;
    }

    private static function validateTotalWeight(array $data): void
    {
        $criteriaProgram = $data['criteriaProgram'] ?? [];

        // Hitung total weight
        $totalWeight = collect($criteriaProgram)->sum(fn($item) => (float) ($item['weight'] ?? 0));

        // Validasi: Total weight tidak boleh lebih dari 100
        if ($totalWeight   !=  100) {
            Log::warning('Total weight exceeds 100%', ['total_weight' => $totalWeight]);
            throw ValidationException::withMessages([
                'criteriaProgram' => 'The total weight of all criteria must not exceed 100%.',
            ]);
        }

        $criteriaIds = array_column($criteriaProgram, 'criteria_id');
        if (count($criteriaIds) !== count(array_unique($criteriaIds))) {
            throw ValidationException::withMessages([
                'criteriaProgram' => 'Setiap kriteria harus unik.',
            ]);
        }
    }
}
