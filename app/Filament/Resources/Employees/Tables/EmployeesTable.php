<?php

namespace App\Filament\Resources\Employees\Tables;

use App\Filament\Exports\EmployeeExporter;
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

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_employee')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('full_name')
                    ->label('ФИО')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('location.city')
                    ->label('Город')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('location.office')
                    ->label('Офис')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('city')
                    ->label('Город')
                    ->options(fn (): array => Location::query()
                        ->select('city')
                        ->distinct()
                        ->orderBy('city')
                        ->pluck('city', 'city')
                        ->all())
                    ->searchable()
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->whereHas(
                            'location',
                            fn (Builder $query): Builder => $query->where('city', $data['value'])
                        )
                    )),

                SelectFilter::make('office')
                    ->label('Офис')
                    ->options(fn (): array => Location::query()
                        ->orderBy('city')
                        ->orderBy('office')
                        ->get()
                        ->groupBy('city')
                        ->map(fn ($locations): array => $locations
                            ->mapWithKeys(fn (Location $location): array => [
                                "{$location->city}|{$location->office}" => $location->office,
                            ])
                            ->all())
                        ->all())
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            filled($data['value'] ?? null),
                            function (Builder $query) use ($data): Builder {
                                [$city, $office] = explode('|', (string) $data['value'], 2);

                                return $query->whereHas(
                                    'location',
                                    fn (Builder $query): Builder => $query
                                        ->where('city', $city)
                                        ->where('office', $office)
                                );
                            }
                        );
                    }),
            ], layout: FiltersLayout::Dropdown)
            ->filtersFormColumns(2)
            ->filtersFormWidth(Width::Large)
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->label('Экспорт CSV')
                    ->modalHeading('Экспорт сотрудников')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->exporter(EmployeeExporter::class),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
