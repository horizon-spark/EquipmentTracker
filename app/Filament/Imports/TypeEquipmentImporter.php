<?php

namespace App\Filament\Imports;

use App\Models\TypeEquipment;
use Filament\Actions\Imports\ImportColumn;

class TypeEquipmentImporter extends BaseImporter
{
    protected static ?string $model = TypeEquipment::class;

    protected static string $entityLabel = 'типов оборудования';

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name_type')
                ->label('Название типа')
                ->requiredMapping()
                ->rules(['required', 'max:45']),
            ImportColumn::make('manufacturer')
                ->label('Производитель')
                ->rules(['nullable', 'max:45']),
            ImportColumn::make('model')
                ->label('Модель')
                ->rules(['nullable', 'max:45']),
        ];
    }

    public function resolveRecord(): TypeEquipment
    {
        if (! $this->shouldUpdateExisting()) {
            return new TypeEquipment;
        }

        return TypeEquipment::firstOrNew([
            'name_type' => $this->data['name_type'],
            'manufacturer' => ($this->data['manufacturer'] ?? null) ?: null,
            'model' => ($this->data['model'] ?? null) ?: null,
        ]);
    }
}
