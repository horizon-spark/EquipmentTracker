<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_equipment')
                    ->label('Оборудование')
                    ->relationship('equipment', 'inv_number')
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('data_service')
                    ->label('Дата обслуживания')
                    ->displayFormat('d.m.Y')
                    ->required(),

                Textarea::make('description')
                    ->label('Описание')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }
}