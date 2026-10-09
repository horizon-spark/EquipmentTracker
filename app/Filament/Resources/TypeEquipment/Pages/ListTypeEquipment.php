<?php

namespace App\Filament\Resources\TypeEquipment\Pages;

use App\Filament\Resources\TypeEquipment\TypeEquipmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTypeEquipment extends ListRecords
{
    protected static string $resource = TypeEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
