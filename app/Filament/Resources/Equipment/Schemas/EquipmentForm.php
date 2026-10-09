<?php

namespace App\Filament\Resources\Equipment\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EquipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('inv_number')
                    ->label('Инвентарный номер')
                    ->required()
                    ->maxLength(20),

                Select::make('id_type')
                    ->label('Тип оборудования')
                    ->relationship('type', 'name_type')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('id_employee')
                    ->label('Сотрудник')
                    ->relationship('employee', 'full_name')
                    ->searchable()
                    ->preload(),

                DatePicker::make('assignment_date')
                    ->label('Дата выдачи')
                    ->displayFormat('d.m.Y'),
            ]);
    }
}