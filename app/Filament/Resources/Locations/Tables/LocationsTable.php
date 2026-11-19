<?php

namespace App\Filament\Resources\Locations\Tables;

use App\Filament\Exports\LocationExporter;
use App\Models\Location;
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

class LocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_location')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('city')
                    ->label('Город')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('office')
                    ->label('Офис')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('address')
                    ->label('Адрес')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('office')
                    ->label('Офис')
                    ->options(fn (): array => Location::query()
                        ->orderBy('city')
                        ->orderBy('office')
                        ->get()
                        ->mapWithKeys(fn (Location $location): array => [
                            $location->id_location => "{$location->city} — {$location->office}",
                        ])
                        ->all())
                    ->searchable()
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->whereKey($data['value'])
                    )),
            ], layout: FiltersLayout::Dropdown)
            ->filtersFormColumns(1)
            ->filtersFormWidth(Width::Large)
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->label('Экспорт CSV')
                    ->modalHeading('Экспорт локаций')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->exporter(LocationExporter::class),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
