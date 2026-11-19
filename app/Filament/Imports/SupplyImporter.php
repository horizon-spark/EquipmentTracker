<?php

namespace App\Filament\Imports;

use App\Models\Supply;
use Filament\Actions\Imports\ImportColumn;

class SupplyImporter extends BaseImporter
{
    protected static ?string $model = Supply::class;

    protected static string $entityLabel = 'расходников';

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name_supplies')
                ->label('Название')
                ->requiredMapping()
                ->rules(['required', 'max:45']),
            ImportColumn::make('quantity')
                ->label('Остаток')
                ->requiredMapping()
                ->integer()
                ->rules(['required', 'integer', 'min:0']),
        ];
    }

    public function resolveRecord(): Supply
    {
        if (! $this->shouldUpdateExisting()) {
            return new Supply;
        }

        return Supply::firstOrNew([
            'name_supplies' => $this->data['name_supplies'],
        ]);
    }
}
