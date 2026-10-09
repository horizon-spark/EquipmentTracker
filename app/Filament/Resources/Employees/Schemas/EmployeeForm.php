<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->label('ФИО')
                    ->required()
                    ->maxLength(45),

                Select::make('id_location')
                    ->label('Локация')
                    ->relationship('location', 'city')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}