<?php

namespace App\Filament\Resources\Locations\Pages;

use App\Filament\Imports\LocationImporter;
use App\Filament\Resources\Locations\LocationResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListLocations extends ListRecords
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->label('Импорт CSV')
                ->modalHeading('Импорт локаций')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->modalDescription('Загрузите CSV-файл и сопоставьте его колонки с полями.')
                ->importer(LocationImporter::class),
            CreateAction::make(),
        ];
    }
}
