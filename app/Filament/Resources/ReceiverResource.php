<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReceiverResource\Pages;
use App\Models\Parameter;
use App\Models\Receiver;
use Filament\Forms\Components\DatePicker;
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
use Illuminate\Database\Eloquent\Builder;


class ReceiverResource extends Resource
{
    protected static ?string $model = Receiver::class;

    public static ?string $pluralModelLabel = 'Data Alternatif';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

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
                DatePicker::make('tanggal_lahir')
                    ->native(false)
                    ->required(),
                Select::make('pekerjaan')
                    ->options(
                        Parameter::where('criteria_id', '=', 3)->pluck('title', 'title')->toArray()
                    )->native(false)->required(),
                Select::make('penghasilan')
                    ->options(
                        Parameter::where('criteria_id', '=', 1)->pluck('title', 'title')->toArray()
                    )->native(false)->required(),
                Select::make('status_tempat_tinggal')
                    ->options(
                        Parameter::where('criteria_id', '=', 4)->pluck('title', 'title')->toArray()
                    )->native(false)->required(),
                Select::make('status_perkawinan')
                    ->options(
                        Parameter::where('criteria_id', '=', 5)->pluck('title', 'title')->toArray()
                    )->native(false)->required(),
                Select::make('jumlah_tanggungan')
                    ->options(
                        Parameter::where('criteria_id', '=', 6)->pluck('title', 'title')->toArray()
                    )->native(false)->required(),
                Select::make('keadaan_rumah')
                    ->options(
                        Parameter::where('criteria_id', '=', 7)->pluck('title', 'title')->toArray()
                    )->native(false)->required(),
                Select::make('disabilitas')
                    ->options(
                        Parameter::where('criteria_id', '=', 8)->pluck('title', 'title')->toArray()
                    )->native(false)->required(),
                Select::make('pendidikan')
                    ->options(
                        Parameter::where('criteria_id', '=', 9)->pluck('title', 'title')->toArray()
                    )->native(false)->required(),
                Select::make('fasilitas_mck')
                    ->options(
                        Parameter::where('criteria_id', '=', 10)->pluck('title', 'title')->toArray()
                    )->native(false)->required(),
                Select::make('bahan_bakar_harian')
                    ->options(
                        Parameter::where('criteria_id', '=', 11)->pluck('title', 'title')->toArray()
                    )->native(false)->required(),
                Select::make('kepemilikan_kendaraan')
                    ->options(
                        Parameter::where('criteria_id', '=', 12)->pluck('title', 'title')->toArray()
                    )->native(false)->required(),

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
                    ->visible(fn ($record) => $record->status === 'Rejected')
                    ->extraAttributes(fn ($state) => [
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
                    ->visible(fn ($record) => $record->status === 'Draft')
                    ->extraAttributes(fn ($state) => [
                        'class' => match ($state) {
                            'Approved' => 'text-green-600',
                            'Draft' => 'text-red-600',
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
            
                return $query->whereNot('status','Draft');
            })
            ->recordUrl(fn ($record) => Pages\ViewReceivers::getUrl(['record' => $record]))
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
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('jumlah_tanggungan')
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
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Approved' => 'success',
                        'Need Approval' => 'warning',
                        'Draft' => 'primary',
                        'Rejected' => 'danger',
                    })
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => in_array($record->status, ['Rejected', 'Draft']))
                    ->color('primary')
                    ->button() ,
                Action::make('approveReject')
                    ->label('Tinjau')
                    ->icon('heroicon-o-check-circle')
                    ->color('primary') // 'success', 'danger', 'warning', 'secondary', 'gray', 'info'
                    ->iconPosition('after') // atau 'after'
                    ->visible(fn ($record) => $record->status === 'Need Approval' && auth()->user()?->hasRole('Staff Kecamatan'))
                    ->button() 
                    ->form([Radio::make('status')
                                ->options([
                                            'Approved' => 'Approve',
                                            'Rejected' => 'Reject'
                                            ])
                                ->inline()
                                ->required()
                                ->reactive() 
                                ->afterStateUpdated(fn ($state, callable $set) => $state !== 'Rejected' ? $set('note', null) : null)
                                ->extraAttributes(fn ($state) => [
                                    'class' => match ($state) {
                                        'Approved' => 'text-green-600',
                                        'Rejected' => 'text-red-600',
                                        default => '',
                                    },
                                ]),
                            Textarea::make('remark')
                                ->label('Catatan')
                                ->visible(fn ($get) => $get('status') === 'Rejected')
                    ])
                    ->action(function (array $data, $record) {
                        $record->status = $data['status'];
                        $record->remark = $data['remark'] ?? null;
                        $record->save();
                    })
                
                ])
                ->bulkActions([
                    match (request()->query('activeTab')) {
                        'draft' => Tables\Actions\BulkActionGroup::make([
                            Tables\Actions\DeleteBulkAction::make(),
                            // Tambah bulk action lain khusus tab "draft" di sini
                        ]),
                        default => Tables\Actions\BulkActionGroup::make([]), 
                        // atau bisa juga tidak ditampilkan sama sekali
                    }
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
        return auth()->user()?->hasRole(['Super Admin','Admin Kecamatan', 'Staff Desa','Staff Kecamatan']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(['Super Admin','Admin Kecamatan', 'Staff Desa','Staff Kecamatan']);
    }
}
