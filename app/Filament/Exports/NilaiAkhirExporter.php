<?php

namespace App\Filament\Exports;

use App\Models\NilaiAkhir;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class NilaiAkhirExporter extends Exporter
{
    protected static ?string $model = NilaiAkhir::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('penerima.nik')->label('NIK'),
            ExportColumn::make('pekerjaan'),
            ExportColumn::make('penghasilan'),
            ExportColumn::make('status_tempat_tinggal'),
            ExportColumn::make('status_perkawinan'),
            ExportColumn::make('jumlah_tanggungan'),
            ExportColumn::make('keadaan_rumah'),
            ExportColumn::make('disabilitas'),
            ExportColumn::make('pendidikan'),
            ExportColumn::make('fasilitas_mck'),
            ExportColumn::make('bahan_bakar_harian'),
            ExportColumn::make('kepemilikan_kendaraan'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your nilai akhir export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
