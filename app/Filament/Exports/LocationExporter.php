<?php

namespace App\Filament\Exports;

use App\Models\Location;
use Filament\Actions\Exports\ExportColumn;

class LocationExporter extends BaseExporter
{
    protected static ?string $model = Location::class;

    protected static string $entityLabel = 'локаций';

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id_location')
                ->label('ID'),
            ExportColumn::make('city')
                ->label('Город'),
            ExportColumn::make('office')
                ->label('Офис'),
            ExportColumn::make('address')
                ->label('Адрес'),
        ];
    }
}
