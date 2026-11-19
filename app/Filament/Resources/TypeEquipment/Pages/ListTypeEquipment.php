<?php

namespace App\Filament\Resources\TypeEquipment\Pages;

use App\Filament\Imports\TypeEquipmentImporter;
use App\Filament\Resources\TypeEquipment\TypeEquipmentResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListTypeEquipment extends ListRecords
{
    protected static string $resource = TypeEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->label('Импорт CSV')
                ->modalHeading('Импорт типов оборудования')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->modalDescription('Загрузите CSV-файл и сопоставьте его колонки с полями.')
                ->importer(TypeEquipmentImporter::class),
            CreateAction::make(),
        ];
    }
}
