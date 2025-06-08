<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeriodResource\Pages;
use App\Models\Period;
use App\Models\Program;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class PeriodResource extends Resource
{
    protected static ?string $model = Period::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Period';
    public static function getNavigationGroup(): ?string
    {
        return 'Assessment';
    }

    public static function getNavigationSort(): int
    {
        return 1;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->unique(table: 'periods', column: 'name', ignoreRecord: true),
                Select::make('program_id')
                    ->label('Program')
                    ->options(
                        Program::where('is_active', true)
                        ->whereNull('deleted_at')
                        ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required()
                    ->rules([
                        'exists:programs,id,deleted_at,NULL'
                    ]),
                TextInput::make('status')
                    ->label('Status')
                    ->default('Active') 
                    ->disabled()
                    ->dehydrated(),
                TextInput::make('description'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(
            Period::query()
                    ->orderByRaw('COALESCE(updated_at, created_at) DESC'))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('program.name')
                    ->searchable()
                    ->sortable(),
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
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'info',
                        'Review' => 'warning',
                        'Done' => 'success',
                    }),

            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(fn () => Period::query()->distinct()->pluck('status', 'status')),
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
                Tables\Actions\EditAction::make()
                ->visible(fn($record) => $record->status === 'Active')
                ->button(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeriods::route('/'),
            'create' => Pages\CreatePeriod::route('/create'),
            'edit' => Pages\EditPeriod::route('/{record}/edit'),
            'view' => Pages\ViewPeriods::route('/{record}'),
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
