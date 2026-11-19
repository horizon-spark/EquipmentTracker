<?php

namespace App\Filament\Exports;

use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

abstract class BaseExporter extends Exporter
{
    protected static string $entityLabel = 'записей';

    public static function getCompletedNotificationTitle(Export $export): string
    {
        return 'Экспорт '.static::$entityLabel.' завершён';
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Экспортировано строк: '.Number::format($export->successful_rows).'.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' Ошибок: '.Number::format($failedRowsCount).'.';
        }

        return $body;
    }

    /**
     * @return array<ExportFormat>
     */
    public function getFormats(): array
    {
        return [ExportFormat::Csv];
    }
}
