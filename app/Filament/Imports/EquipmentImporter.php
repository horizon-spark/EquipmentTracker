<?php

namespace App\Filament\Imports;

use App\Models\Employee;
use App\Models\Equipment;
use App\Models\TypeEquipment;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;

class EquipmentImporter extends BaseImporter
{
    protected static ?string $model = Equipment::class;

    protected static string $entityLabel = 'оборудования';

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('inv_number')
                ->label('Инвентарный номер')
                ->requiredMapping()
                ->rules(['required', 'max:20']),
            ImportColumn::make('serial_number')
                ->label('Серийный номер')
                ->rules(['nullable', 'max:45'])
                ->ignoreBlankState(),
            ImportColumn::make('name_type')
                ->label('Тип')
                ->requiredMapping()
                ->rules(['required', 'max:45'])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('manufacturer')
                ->label('Производитель')
                ->rules(['nullable', 'max:45'])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('model')
                ->label('Модель')
                ->rules(['nullable', 'max:45'])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('full_name')
                ->label('Сотрудник')
                ->rules(['nullable', 'max:45'])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('assignment_date')
                ->label('Дата выдачи')
                ->castStateUsing(fn (mixed $state): ?string => static::parseDate($state))
                ->rules(['nullable', 'date'])
                ->ignoreBlankState(),
        ];
    }

    public function resolveRecord(): Equipment
    {
        $type = TypeEquipment::firstOrCreate([
            'name_type' => $this->data['name_type'],
            'manufacturer' => ($this->data['manufacturer'] ?? null) ?: null,
            'model' => ($this->data['model'] ?? null) ?: null,
        ]);

        $equipment = $this->shouldUpdateExisting()
            ? Equipment::firstOrNew(['inv_number' => $this->data['inv_number']])
            : new Equipment;

        $equipment->type()->associate($type);

        $fullName = trim((string) ($this->data['full_name'] ?? ''));

        if ($fullName !== '') {
            $employee = Employee::query()
                ->where('full_name', $fullName)
                ->first();

            if (! $employee) {
                throw new RowImportFailedException(
                    "Сотрудник «{$fullName}» не найден. Сначала импортируйте сотрудников."
                );
            }

            $equipment->employee()->associate($employee);
        }

        return $equipment;
    }
}
