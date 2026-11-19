<?php

namespace App\Filament\Resources\Locations\Tables;

use App\Filament\Exports\LocationExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_location')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('city')
                    ->label('Город')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('office')
                    ->label('Офис')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('address')
                    ->label('Адрес')
                    ->searchable(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Экспорт CSV')
                    ->modalHeading('Экспорт локаций')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->exporter(LocationExporter::class),
            ])
            ->filters([
                //
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
