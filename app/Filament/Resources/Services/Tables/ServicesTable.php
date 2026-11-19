<?php

namespace App\Filament\Resources\Services\Tables;

use App\Filament\Exports\ServiceExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_service')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('service_date')
                    ->label('Дата')
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('equipment.inv_number')
                    ->label('Оборудование')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('equipment.type.name_type')
                    ->label('Тип')
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Описание')
                    ->limit(50)
                    ->searchable(),
            ])
            ->defaultSort('service_date', 'desc')   // свежие сверху
            ->headerActions([
                ExportAction::make()
                    ->label('Экспорт CSV')
                    ->modalHeading('Экспорт обслуживаний')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->exporter(ServiceExporter::class),
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
