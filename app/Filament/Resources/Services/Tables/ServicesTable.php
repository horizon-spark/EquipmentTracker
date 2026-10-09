<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_service')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('data_service')
                    ->label('Дата')
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('equipment.inv_number')
                    ->label('Оборудование')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('equipment.type.name_type')
                    ->label('Тип')
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Описание')
                    ->limit(50)
                    ->searchable(),
            ])
            ->defaultSort('data_service', 'desc')   // свежие сверху
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