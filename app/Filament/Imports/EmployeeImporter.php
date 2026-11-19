<?php

namespace App\Filament\Imports;

use App\Models\Employee;
use App\Models\Location;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;

class EmployeeImporter extends BaseImporter
{
    protected static ?string $model = Employee::class;

    protected static string $entityLabel = 'сотрудников';

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('full_name')
                ->label('ФИО')
                ->requiredMapping()
                ->rules(['required', 'max:45']),
            ImportColumn::make('city')
                ->label('Город')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('office')
                ->label('Офис')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address')
                ->label('Адрес (для новой локации)')
                ->rules(['nullable', 'max:255'])
                ->fillRecordUsing(fn () => null),
        ];
    }

    public function resolveRecord(): Employee
    {
        $location = Location::query()
            ->where('city', $this->data['city'])
            ->where('office', $this->data['office'])
            ->first();

        if (! $location) {
            $address = trim((string) ($this->data['address'] ?? ''));

            if ($address === '') {
                throw new RowImportFailedException(
                    "Локация «{$this->data['city']}, {$this->data['office']}» не найдена. Заполните адрес, чтобы создать её."
                );
            }

            $location = Location::create([
                'city' => $this->data['city'],
                'office' => $this->data['office'],
                'address' => $address,
            ]);
        }

        $employee = $this->shouldUpdateExisting()
            ? Employee::query()
                ->where('full_name', $this->data['full_name'])
                ->where('id_location', $location->id_location)
                ->first()
            : null;

        $employee ??= new Employee;
        $employee->location()->associate($location);

        return $employee;
    }
}
