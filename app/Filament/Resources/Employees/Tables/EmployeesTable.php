<?php

namespace App\Filament\Resources\Employees\Tables;

use App\Filament\Exports\EmployeeExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_employee')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('full_name')
                    ->label('ФИО')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('location.city')
                    ->label('Город')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('location.office')
                    ->label('Офис')
                    ->searchable(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Экспорт CSV')
                    ->modalHeading('Экспорт сотрудников')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->exporter(EmployeeExporter::class),
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
