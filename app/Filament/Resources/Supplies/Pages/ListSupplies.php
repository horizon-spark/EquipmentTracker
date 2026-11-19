<?php

namespace App\Filament\Resources\Supplies\Pages;

use App\Filament\Imports\SupplyImporter;
use App\Filament\Resources\Supplies\SupplyResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListSupplies extends ListRecords
{
    protected static string $resource = SupplyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->label('Импорт CSV')
                ->modalHeading('Импорт расходников')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->modalDescription('Загрузите CSV-файл и сопоставьте его колонки с полями.')
                ->importer(SupplyImporter::class),
            CreateAction::make(),
        ];
    }
}
