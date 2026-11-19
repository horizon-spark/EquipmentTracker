<?php

namespace App\Filament\Resources\Equipment\Tables;

use App\Filament\Exports\EquipmentExporter;
use App\Models\Location;
use App\Models\TypeEquipment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

                TextColumn::make('serial_number')
                    ->label('Серийный номер')
                    ->searchable()
                    ->toggleable(),

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
            ->filters([
                SelectFilter::make('id_employee')
                    ->label('Сотрудник')
                    ->relationship('employee', 'full_name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('id_type')
                    ->label('Тип оборудования')
                    ->options(fn (): array => TypeEquipment::query()
                        ->orderBy('name_type')
                        ->get()
                        ->mapWithKeys(fn (TypeEquipment $type): array => [
                            $type->id_type => trim(implode(' ', array_filter([
                                $type->name_type,
                                $type->manufacturer,
                                $type->model,
                            ]))),
                        ])
                        ->all())
                    ->searchable()
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->where('id_type', $data['value'])
                    )),

                SelectFilter::make('id_location')
                    ->label('Локация')
                    ->options(fn (): array => Location::query()
                        ->orderBy('city')
                        ->orderBy('office')
                        ->get()
                        ->mapWithKeys(fn (Location $location): array => [
                            $location->id_location => "{$location->city}, {$location->office}",
                        ])
                        ->all())
                    ->searchable()
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->whereHas(
                            'employee.location',
                            fn (Builder $query): Builder => $query->whereKey($data['value'])
                        )
                    )),
            ], layout: FiltersLayout::Dropdown)
            ->filtersFormColumns(2)
            ->filtersFormWidth(Width::Large)
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->label('Экспорт CSV')
                    ->modalHeading('Экспорт оборудования')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->exporter(EquipmentExporter::class),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
