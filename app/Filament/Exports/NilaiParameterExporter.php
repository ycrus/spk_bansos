<?php

namespace App\Filament\Exports;

use App\Models\NilaiBobot;
use App\Models\NilaiParameter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class NilaiParameterExporter extends Exporter
{
    protected static ?string $model = NilaiBobot::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('penerima.nik')->label('NIK'),
            ExportColumn::make('penerima.nama')->label('Nama'),
            ExportColumn::make('penerima.desa.name')->label('Kelurahan'),
            ExportColumn::make('pekerjaan')->label('Pekerjaan'),
            ExportColumn::make('penghasilan')->label('Penghasilan'),
            ExportColumn::make('status_tempat_tinggal')->label('Status Tempat Tinggal'),
            ExportColumn::make('status_perkawinan')->label('Jumlah Anggota Balita/Anak Sekolah/Lansia'),
            ExportColumn::make('jumlah_tanggungan')->label('Jumlah Anggota Keluarga'),
            ExportColumn::make('keadaan_rumah')->label('Keadaan Rumah'),
            ExportColumn::make('disabilitas')->label('Jumlah Anggota Disabilitas'),
            ExportColumn::make('pendidikan')->label('Pendidikan'),
            ExportColumn::make('fasilitas_mck')->label('Fasilitas MCK'),
            ExportColumn::make('bahan_bakar_harian')->label('Bahan Bakar Masak'),
            ExportColumn::make('kepemilikan_kendaraan')->label('Kepemilikan Kendaraan'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your nilai parameter export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
