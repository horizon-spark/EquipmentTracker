<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Imports\ServiceImporter;
use App\Filament\Resources\Services\ServiceResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->label('Импорт CSV')
                ->modalHeading('Импорт обслуживаний')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->modalDescription('Загрузите CSV-файл и сопоставьте его колонки с полями. Расходники при импорте обслуживаний не списываются.')
                ->importer(ServiceImporter::class),
            CreateAction::make(),
        ];
    }
}
