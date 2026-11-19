<?php

namespace App\Filament\Exports;

use App\Models\Service;
use App\Models\Supply;
use Filament\Actions\Exports\ExportColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ServiceExporter extends BaseExporter
{
    protected static ?string $model = Service::class;

    protected static string $entityLabel = 'обслуживаний';

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id_service')
                ->label('ID'),
            ExportColumn::make('service_date')
                ->label('Дата обслуживания')
                ->formatStateUsing(
                    fn (mixed $state): ?string => filled($state) ? Carbon::parse($state)->format('d.m.Y') : null
                ),
            ExportColumn::make('equipment.inv_number')
                ->label('Инвентарный номер'),
            ExportColumn::make('description')
                ->label('Описание'),
            ExportColumn::make('supplies_summary')
                ->label('Расходники')
                ->getStateUsing(fn (Service $record): string => $record->supplies
                    ->map(fn (Supply $supply): string => "{$supply->name_supplies} x{$supply->pivot->quantity_used}")
                    ->implode('; ')),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with(['equipment', 'supplies']);
    }
}
