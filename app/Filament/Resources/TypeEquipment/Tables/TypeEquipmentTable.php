<?php

namespace App\Filament\Resources\TypeEquipment\Tables;

use App\Filament\Exports\TypeEquipmentExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TypeEquipmentTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_type')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name_type')
                    ->label('Название типа')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('manufacturer')
                    ->label('Производитель')
                    ->searchable(),

                TextColumn::make('model')
                    ->label('Модель')
                    ->searchable(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Экспорт CSV')
                    ->modalHeading('Экспорт типов оборудования')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->exporter(TypeEquipmentExporter::class),
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
