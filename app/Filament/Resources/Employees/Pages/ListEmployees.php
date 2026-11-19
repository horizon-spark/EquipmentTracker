<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Imports\EmployeeImporter;
use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->label('Импорт CSV')
                ->modalHeading('Импорт сотрудников')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->modalDescription('Загрузите CSV-файл и сопоставьте его колонки с полями.')
                ->importer(EmployeeImporter::class),
            CreateAction::make(),
        ];
    }
}
