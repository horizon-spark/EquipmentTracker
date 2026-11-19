<?php

namespace App\Filament\Exports;

use App\Models\Supply;
use Filament\Actions\Exports\ExportColumn;

class SupplyExporter extends BaseExporter
{
    protected static ?string $model = Supply::class;

    protected static string $entityLabel = 'расходников';

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id_supply')
                ->label('ID'),
            ExportColumn::make('name_supplies')
                ->label('Название'),
            ExportColumn::make('quantity')
                ->label('Остаток'),
        ];
    }
}
