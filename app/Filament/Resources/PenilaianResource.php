<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenilaianResource\Pages;
use App\Filament\Resources\PenilaianResource\RelationManagers\CalonPenerimaRelationManager;
use App\Filament\Resources\PenilaianResource\RelationManagers\ReceiverPeriodRelationManager;
use App\Models\CalonPenerima;
use App\Models\Penilaian;
use App\Models\Period;
use App\Models\Receiver;
use App\Services\PenilaianService;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class PenilaianResource extends Resource
{
    protected static ?string $model = Penilaian::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';
    protected static ?string $navigationLabel = 'Penilaian';
    public static function getNavigationGroup(): ?string
    {
        return 'Assessment';
    }

    public static function getNavigationSort(): int
    {
        return 2;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('period_id')
                    ->label('Period')
                    ->relationship('period', 'name')
                    ->options(Period::where('status', 'Active')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->afterStateUpdated(function ($state) {
                        if ($state) {
                            Period::where('id', $state)->update(['status' => 'Review']);
                        }
                    }),
                TextInput::make('status')
                    ->default("Active")
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Penilaian::query()
                        ->with('period')
                        ->withCount('dataPenerima')
                        ->orderByRaw('COALESCE(updated_at, created_at) DESC'))
            ->columns([
                TextColumn::make('period.name')
                    ->sortable(),
                TextColumn::make('jumlah_penerima')
                    ->sortable()
                    ->alignment('center'),
                TextColumn::make('data_penerima_count')
                    ->label('Jumlah Data Alternatif')
                    ->sortable()
                    ->alignment('center'),
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
                        'Active' => 'success',
                        'Review' => 'warning',
                        'Done' => 'primary',
                    }),
            ])
            ->recordUrl(function ($record) {
                return $record->status === 'Active'
                    ? static::getUrl('edit', ['record' => $record])
                    : null;
            })
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
                    ->button()
                    ->visible(fn($record) => $record->status === 'Active'),
                Action::make('startButton')
                    ->label('Start')
                    ->action(function ($record) {
                        $service = app(PenilaianService::class);
                        $service->startCalculate($record->id);

                        $record->status = 'Done';
                        $record->save();

                        Period::where('id', $record->period_id)->update(['status' => 'Done']);

                        session()->flash('success', 'Calculation started');
                    })                    
                    ->color('danger')
                    ->icon('heroicon-o-link')
                    ->button()
                    ->visible(fn($record) => $record->status === 'Active' && $record->dataPenerima()->count() > 0),
            ])
            ->bulkActions([
               
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ReceiverPeriodRelationManager::class,
            CalonPenerimaRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenilaians::route('/'),
            'create' => Pages\CreatePenilaian::route('/create'),
            'edit' => Pages\EditPenilaian::route('/{record}/edit'),
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

    public static function afterCreate(Model $record): void
    {
        $approvedAlternatives = Receiver::where('status', 'Approved')->get();

        foreach ($approvedAlternatives as $alternative) {
            CalonPenerima::create([
                'penilaian_id' => $record->id,
                'receiver_id' => $alternative->id,
            ]);
        }
    }
}
