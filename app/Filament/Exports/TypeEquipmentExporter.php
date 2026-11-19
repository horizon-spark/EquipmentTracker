<?php

namespace App\Filament\Exports;

use App\Models\TypeEquipment;
use Filament\Actions\Exports\ExportColumn;

class TypeEquipmentExporter extends BaseExporter
{
    protected static ?string $model = TypeEquipment::class;

    protected static string $entityLabel = 'типов оборудования';

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id_type')
                ->label('ID'),
            ExportColumn::make('name_type')
                ->label('Название типа'),
            ExportColumn::make('manufacturer')
                ->label('Производитель'),
            ExportColumn::make('model')
                ->label('Модель'),
        ];
    }
}
