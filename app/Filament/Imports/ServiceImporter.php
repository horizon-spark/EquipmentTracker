<?php

namespace App\Filament\Imports;

use App\Models\Equipment;
use App\Models\Service;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;

class ServiceImporter extends BaseImporter
{
    protected static ?string $model = Service::class;

    protected static string $entityLabel = 'обслуживаний';

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('equipment_inv_number')
                ->label('Инвентарный номер оборудования')
                ->requiredMapping()
                ->rules(['required', 'max:20'])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('service_date')
                ->label('Дата обслуживания')
                ->requiredMapping()
                ->castStateUsing(fn (mixed $state): ?string => static::parseDate($state))
                ->rules(['required', 'date']),
            ImportColumn::make('description')
                ->label('Описание')
                ->rules(['nullable', 'max:255'])
                ->ignoreBlankState(),
        ];
    }

    public function resolveRecord(): Service
    {
        $equipment = Equipment::query()
            ->where('inv_number', $this->data['equipment_inv_number'])
            ->first();

        if (! $equipment) {
            throw new RowImportFailedException(
                "Оборудование с инвентарным номером «{$this->data['equipment_inv_number']}» не найдено."
            );
        }

        $service = $this->shouldUpdateExisting()
            ? Service::firstOrNew([
                'id_equipment' => $equipment->id_equipment,
                'service_date' => $this->data['service_date'],
                'description' => $this->data['description'] ?? null,
            ])
            : new Service;

        $service->equipment()->associate($equipment);

        return $service;
    }
}
