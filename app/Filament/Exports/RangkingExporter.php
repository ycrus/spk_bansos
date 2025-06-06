<?php

namespace App\Filament\Exports;

use App\Models\Rangking;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class RangkingExporter extends Exporter
{
    protected static ?string $model = Rangking::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('penerima.nik'),
            ExportColumn::make('penerima.nama')->label('Nama'),
            ExportColumn::make('penerima.desa.name')->label('Kelurahan'),
            ExportColumn::make('total'),
            ExportColumn::make('ranking')
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your rangking export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
