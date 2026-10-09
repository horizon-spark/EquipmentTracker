<?php

namespace App\Filament\Resources\Supplies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupplyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name_supplies')
                    ->label('Название расходника')
                    ->required()
                    ->maxLength(45),

                TextInput::make('quantity')
                    ->label('Количество')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
            ]);
    }
}