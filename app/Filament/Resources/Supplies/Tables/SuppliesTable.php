<?php

namespace App\Filament\Resources\Supplies\Tables;

use App\Filament\Exports\SupplyExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SuppliesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_supply')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name_supplies')
                    ->label('Название расходника')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Количество')
                    ->numeric()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->label('Экспорт CSV')
                    ->modalHeading('Экспорт расходников')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->exporter(SupplyExporter::class),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
