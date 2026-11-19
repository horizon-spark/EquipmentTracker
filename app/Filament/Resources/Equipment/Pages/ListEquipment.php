<?php

namespace App\Filament\Resources\Equipment\Pages;

use App\Filament\Imports\EquipmentImporter;
use App\Filament\Resources\Equipment\EquipmentResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListEquipment extends ListRecords
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->label('Импорт CSV')
                ->modalHeading('Импорт оборудования')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->modalDescription('Загрузите CSV-файл и сопоставьте его колонки с полями.')
                ->importer(EquipmentImporter::class),
            CreateAction::make(),
        ];
    }
}
