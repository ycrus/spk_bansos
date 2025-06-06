<?php

namespace App\Filament\Exports;

use App\Models\Receiver;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ReceiverExporter extends Exporter
{
    protected static ?string $model = Receiver::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nik'),
            ExportColumn::make('nama')->label('Nama'),
            ExportColumn::make('desa.name')->label('Kelurahan'),
            ExportColumn::make('alamat'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your result export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
