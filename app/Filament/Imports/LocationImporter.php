<?php

namespace App\Filament\Imports;

use App\Models\Location;
use Filament\Actions\Imports\ImportColumn;

class LocationImporter extends BaseImporter
{
    protected static ?string $model = Location::class;

    protected static string $entityLabel = 'локаций';

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('city')
                ->label('Город')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('office')
                ->label('Офис')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('address')
                ->label('Адрес')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): Location
    {
        if (! $this->shouldUpdateExisting()) {
            return new Location;
        }

        return Location::firstOrNew([
            'city' => $this->data['city'],
            'office' => $this->data['office'],
        ]);
    }
}
