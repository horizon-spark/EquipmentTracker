<?php

namespace App\Filament\Exports;

use App\Models\Equipment;
use Filament\Actions\Exports\ExportColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class EquipmentExporter extends BaseExporter
{
    protected static ?string $model = Equipment::class;

    protected static string $entityLabel = 'оборудования';

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id_equipment')
                ->label('ID'),
            ExportColumn::make('inv_number')
                ->label('Инвентарный номер'),
            ExportColumn::make('serial_number')
                ->label('Серийный номер'),
            ExportColumn::make('type.name_type')
                ->label('Тип'),
            ExportColumn::make('type.manufacturer')
                ->label('Производитель'),
            ExportColumn::make('type.model')
                ->label('Модель'),
            ExportColumn::make('employee.full_name')
                ->label('Сотрудник'),
            ExportColumn::make('employee.location.city')
                ->label('Город'),
            ExportColumn::make('employee.location.office')
                ->label('Офис'),
            ExportColumn::make('assignment_date')
                ->label('Дата выдачи')
                ->formatStateUsing(
                    fn (mixed $state): ?string => filled($state) ? Carbon::parse($state)->format('d.m.Y') : null
                ),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with(['type', 'employee.location']);
    }
}
