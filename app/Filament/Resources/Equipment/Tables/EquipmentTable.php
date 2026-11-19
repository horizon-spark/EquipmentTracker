<?php

namespace App\Filament\Resources\Equipment\Tables;

use App\Filament\Exports\EquipmentExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EquipmentTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_equipment')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('inv_number')
                    ->label('Инв. номер')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('serial_number')
                    ->label('Серийный номер')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('type.name_type')
                    ->label('Тип')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('employee.full_name')
                    ->label('Сотрудник')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('assignment_date')
                    ->label('Дата выдачи')
                    ->date('d.m.Y')
                    ->sortable(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Экспорт CSV')
                    ->modalHeading('Экспорт оборудования')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->exporter(EquipmentExporter::class),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
