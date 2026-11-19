<?php

namespace App\Filament\Exports;

use App\Models\Employee;
use Filament\Actions\Exports\ExportColumn;
use Illuminate\Database\Eloquent\Builder;

class EmployeeExporter extends BaseExporter
{
    protected static ?string $model = Employee::class;

    protected static string $entityLabel = 'сотрудников';

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id_employee')
                ->label('ID'),
            ExportColumn::make('full_name')
                ->label('ФИО'),
            ExportColumn::make('location.city')
                ->label('Город'),
            ExportColumn::make('location.office')
                ->label('Офис'),
            ExportColumn::make('location.address')
                ->label('Адрес'),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with('location');
    }
}
