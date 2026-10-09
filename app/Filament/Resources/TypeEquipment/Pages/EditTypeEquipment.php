<?php

namespace App\Filament\Resources\TypeEquipment\Pages;

use App\Filament\Resources\TypeEquipment\TypeEquipmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTypeEquipment extends EditRecord
{
    protected static string $resource = TypeEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
