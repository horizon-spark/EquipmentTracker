<?php

namespace App\Filament\Resources\TypeEquipment\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TypeEquipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name_type')
                    ->label('Название типа')
                    ->required()
                    ->maxLength(45),

                TextInput::make('manufacturer')
                    ->label('Производитель')
                    ->maxLength(45),

                TextInput::make('model')
                    ->label('Модель')
                    ->maxLength(45),

                TextInput::make('serial_number')
                    ->label('Серийный номер')
                    ->maxLength(45),
            ]);
    }
}