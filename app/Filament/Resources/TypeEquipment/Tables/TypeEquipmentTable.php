<?php

namespace App\Filament\Resources\TypeEquipment\Tables;

use App\Filament\Exports\TypeEquipmentExporter;
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
            ])
            ->filters([
                SelectFilter::make('name_type')
                    ->label('Название типа')
                    ->options(fn (): array => TypeEquipment::query()
                        ->select('name_type')
                        ->distinct()
                        ->orderBy('name_type')
                        ->pluck('name_type', 'name_type')
                        ->all())
                    ->searchable()
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->where('name_type', $data['value'])
                    )),

                SelectFilter::make('manufacturer')
                    ->label('Производитель')
                    ->options(fn (): array => TypeEquipment::query()
                        ->select('manufacturer')
                        ->whereNotNull('manufacturer')
                        ->where('manufacturer', '!=', '')
                        ->distinct()
                        ->orderBy('manufacturer')
                        ->pluck('manufacturer', 'manufacturer')
                        ->all())
                    ->searchable()
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->where('manufacturer', $data['value'])
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
                    ->modalHeading('Экспорт типов оборудования')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->exporter(TypeEquipmentExporter::class),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
