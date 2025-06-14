<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramResource\Pages;
use App\Models\Criteria;
use App\Models\Program;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Program')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->unique(table: 'receivers', column: 'nik', ignoreRecord: true),
                            Toggle::make('is_active')
                                ->label('Status')
                                ->inline()
                                ->required(),
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
            ->query(
                Program::query()
                        ->orderByRaw('COALESCE(updated_at, created_at) DESC'))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                ToggleColumn::make('is_active')
                    ->label('Status'),
                TextColumn::make('created_at')
                    ->label('Created Date')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft()
                    ->dateTime('d/m/Y'),
                TextColumn::make('updated_at')
                    ->label('Modified Date')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft()
                    ->dateTime('d/m/Y'),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ]),
                Filter::make('date_filter')
                    ->label('Filter by Date')
                    ->form([
                        Select::make('field')->options([
                            'created_at' => 'Created Date',
                            'updated_at' => 'Modified Date',
                        ])->default('created_at'),
                        DatePicker::make('from')->label('From'),
                        DatePicker::make('to')->label('To'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $field = $data['field'] ?? 'created_at';
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate($field, '>=', $data['from']))
                            ->when($data['to'], fn ($q) => $q->whereDate($field, '<=', $data['to']));
                    }),           
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrograms::route('/'),
            'create' => Pages\CreateProgram::route('/create'),
            'edit' => Pages\EditProgram::route('/{record}/edit'),
            'view' => Pages\ViewPrograms::route('/{record}'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole(['Super Admin','Admin Kecamatan','Staff Kecamatan']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(['Super Admin','Admin Kecamatan','Staff Kecamatan']);
    }
}
