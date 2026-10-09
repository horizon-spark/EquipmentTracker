<?php

namespace App\Filament\Resources\TypeEquipment\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TypeEquipmentTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_type')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name_type')
                    ->label('Название типа')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('manufacturer')
                    ->label('Производитель')
                    ->searchable(),

                TextColumn::make('model')
                    ->label('Модель')
                    ->searchable(),

                TextColumn::make('serial_number')
                    ->label('Серийный номер')
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}