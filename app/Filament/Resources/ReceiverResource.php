<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReceiverResource\Pages;
use App\Models\CalonPenerima;
use App\Models\Parameter;
use App\Models\Penilaian;
use App\Models\Receiver;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class ReceiverResource extends Resource
{
    protected static ?string $model = Receiver::class;
    public static ?string $pluralModelLabel = 'Data Alternatif';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')->required(),
                TextInput::make('nik')
                    ->unique(table: 'receivers', column: 'nik', ignoreRecord: true)
                    ->required()
                    ->numeric(),
                Select::make('kelurahan')
                    ->options(function () {
                        $user = auth()->user();

                        if ($user?->hasRole('Staff Desa')) {
                            return \App\Models\Kelurahan::where('id', $user->desa)->pluck('name', 'id');
                        }
                        return \App\Models\Kelurahan::all()->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required(),
                TextInput::make('alamat')
                    ->label('Alamat'),
                Select::make('pekerjaan')
                    ->options(
                        Parameter::where('criteria_id', '=', 3)->pluck('title', 'title')->toArray()
                    )->native(false),
                Select::make('penghasilan')
                    ->options(
                        Parameter::where('criteria_id', '=', 1)->pluck('title', 'title')->toArray()
                    )->native(false),
                Select::make('status_tempat_tinggal')
                    ->options(
                        Parameter::where('criteria_id', '=', 5)->pluck('title', 'title')->toArray()
                    )->native(false),
                Select::make('status_perkawinan')
                    ->label('Jumlah Anggota Balita/Anak Sekolah/Lansia')
                    ->options(
                        Parameter::where('criteria_id', '=', 4)->pluck('title', 'title')->toArray()
                    )->native(false),
                Select::make('jumlah_tanggungan')
                    ->label('Jumlah Anggota Keluarga')
                    ->options(
                        Parameter::where('criteria_id', '=', 6)->pluck('title', 'title')->toArray()
                    )->native(false),
                Select::make('keadaan_rumah')
                    ->options(
                        Parameter::where('criteria_id', '=', 7)->pluck('title', 'title')->toArray()
                    )->native(false),
                Select::make('disabilitas')
                    ->label('Jumlah Anggota Disabilitas')
                    ->options(
                        Parameter::where('criteria_id', '=', 9)->pluck('title', 'title')->toArray()
                    )->native(false),
                Select::make('pendidikan')
                    ->options(
                        Parameter::where('criteria_id', '=', 10)->pluck('title', 'title')->toArray()
                    )->native(false),
                Select::make('fasilitas_mck')
                    ->options(
                        Parameter::where('criteria_id', '=', 11)->pluck('title', 'title')->toArray()
                    )->native(false),
                Select::make('bahan_bakar_harian')
                    ->options(
                        Parameter::where('criteria_id', '=', 8)->pluck('title', 'title')->toArray()
                    )->native(false),
                Select::make('kepemilikan_kendaraan')
                    ->options(
                        Parameter::where('criteria_id', '=', 12)->pluck('title', 'title')->toArray()
                    )->native(false),
                Textarea::make('remark')
                    ->label('Remark'),
                Radio::make('status')
                    ->options([
                        'Need Approval' => 'Yes',
                        'Rejected' => 'No'
                    ])
                    ->inline()
                    ->label('Data sudah diperbaiki?')
                    ->required()
                    ->reactive()
                    ->visible(fn($record) => $record && $record->status === 'Rejected')
                    ->extraAttributes(fn($state) => [
                        'class' => match ($state) {
                            'Approved' => 'text-green-600',
                            'Rejected' => 'text-red-600',
                            default => '',
                        },
                    ]),

                Radio::make('status')
                    ->options([
                        'Need Approval' => 'Yes',
                        'Draft' => 'No'
                    ])
                    ->inline()
                    ->label('Ajukan Data Alternatif?')
                    ->required()
                    ->reactive() 
                    ->visible(function ($record) {
                        return $record && $record->status === 'Draft';
                    })
                    ->extraAttributes(fn($state) => [
                        'class' => match ($state) {
                            'Approved' => 'text-green-600',
                            'Draft' => 'text-red-600',
                            default => '',
                        },
                    ]),

                Radio::make('status')
                    ->options([
                        'Need Approval' => 'Yes',
                        'Need Update' => 'No'
                    ])
                    ->inline()
                    ->label('Data sudah di update?')
                    ->required()
                    ->reactive()
                    ->visible(fn($record) => $record->status === 'Need Update')
                    ->extraAttributes(fn($state) => [
                        'class' => match ($state) {
                            'Approved' => 'text-green-600',
                            'Need Update' => 'text-red-600',
                            default => '',
                        },
                    ]),

                TextInput::make('status')
                    ->label('Status')
                    ->default('Draft')
                    ->disabled()
                    ->dehydrated(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()?->hasRole('Staff Desa')) {
                    $desaId = auth()->user()?->desa;
                    return $query->where('kelurahan', $desaId);
                }
                return $query->whereNot('status', 'Draft');
            })
            ->query(
                Receiver::query()
                        ->orderByRaw('COALESCE(updated_at, created_at) DESC'))
            ->recordUrl(fn($record) => Pages\ViewReceivers::getUrl(['record' => $record]))
            ->columns([
                TextColumn::make('nik')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('nama')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('desa.name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('pekerjaan')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('penghasilan')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('status_tempat_tinggal')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('status_perkawinan')
                    ->label('Jumlah Anggota Balita/Anak Sekolah/Lansia')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('jumlah_tanggungan')
                    ->label('Jumlah Anggota Keluarga')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('status_tempat_tinggal')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('keadaan_rumah')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('disabilitas')
                    ->label('Jumlah Anggota Disabilitas')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('pendidikan')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('fasilitas_mck')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('bahan_bakar_harian')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('kepemilikan_kendaraan')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
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
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Approved' => 'success',
                        'Need Approval' => 'warning',
                        'Draft' => 'primary',
                        'Rejected' => 'danger',
                        'Need Update' => 'gray',
                    })
            ])
            ->filters([
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
                    ->visible(fn($record) => in_array($record->status, ['Rejected', 'Draft', 'Need Update']))
                    ->color('primary')
                    ->button(),

                Action::make('approveReject')
                    ->label('Review')
                    ->icon('heroicon-o-check-circle')
                    ->color('primary')
                    ->iconPosition('after')
                    ->visible(fn($record) => $record->status === 'Need Approval' && auth()->user()?->hasRole('Staff Kecamatan'))
                    ->button()
                    ->form([
                        Radio::make('status')
                            ->options([
                                'Approved' => 'Approve',
                                'Rejected' => 'Reject'
                            ])
                            ->inline()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn($state, callable $set) => $state !== 'Rejected' ? $set('note', null) : null)
                            ->extraAttributes(fn($state) => [
                                'class' => match ($state) {
                                    'Approved' => 'text-green-600',
                                    'Rejected' => 'text-red-600',
                                    default => '',
                                },
                            ]),
                        Textarea::make('remark')
                            ->label('Catatan')
                            ->visible(fn($get) => $get('status') === 'Rejected')
                    ])
                    ->action(function (array $data, $record) {
                        if ($data['status'] === 'Approved') {
                            $penilaians = Penilaian::where('status', 'Active')->get();
        
                            foreach ($penilaians as $penilaian) {
                                CalonPenerima::firstOrCreate([
                                    'receiver_id' => $record->id,
                                    'penilaian_id' => $penilaian->id,
                                ]);
                            }
                        }
                        
                        $record->status = $data['status'];
                        $record->remark = $data['remark'] ?? null;
                        $record->save();
                    }),

                Action::make('needUpdate')
                    ->label('Request Update')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->iconPosition('after')
                    ->visible(fn($record) => $record->status === 'Approved' && auth()->user()?->hasRole('Staff Kecamatan'))
                    ->button()
                    ->action(function (array $data, $record) {
                        $record->status = 'Need Update';
                        $record->save();                                             
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulkApprove')
                        ->label('Approve All')
                        ->icon('heroicon-o-check-circle')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->visible(fn($livewire) => $livewire->activeTab === 'need'
                            && auth()->user()?->hasRole('Staff Kecamatan'))
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->update(['status' => 'Approved']);
                            }
                        }),
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
            'index' => Pages\ListReceivers::route('/'),
            'create' => Pages\CreateReceiver::route('/create'),
            'edit' => Pages\EditReceiver::route('/{record}/edit'),
            'view' => Pages\ViewReceivers::route('/{record}'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole(['Super Admin', 'Admin Kecamatan', 'Staff Desa', 'Staff Kecamatan']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(['Super Admin', 'Admin Kecamatan', 'Staff Desa', 'Staff Kecamatan']);
    }
}
