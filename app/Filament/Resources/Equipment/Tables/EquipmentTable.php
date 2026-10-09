<?php

namespace App\Filament\Resources\Equipment\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EquipmentTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_equipment')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('inv_number')
                    ->label('Инв. номер')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type.name_type')
                    ->label('Тип')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('employee.full_name')
                    ->label('Сотрудник')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('assignment_date')
                    ->label('Дата выдачи')
                    ->date('d.m.Y')
                    ->sortable(),
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