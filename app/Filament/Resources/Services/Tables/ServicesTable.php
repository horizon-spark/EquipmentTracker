<?php

namespace App\Filament\Resources\Services\Tables;

use App\Filament\Exports\ServiceExporter;
use App\Models\TypeEquipment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_service')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('service_date')
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
            ->defaultSort('service_date', 'desc')   // свежие сверху
            ->filters([
                SelectFilter::make('id_equipment')
                    ->label('Оборудование')
                    ->relationship('equipment', 'inv_number')
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
                        fn (Builder $query): Builder => $query->whereHas(
                            'equipment.type',
                            fn (Builder $query): Builder => $query->whereKey($data['value'])
                        )
                    )),

                Filter::make('service_date')
                    ->label('Период обслуживания')
                    ->form([
                        DatePicker::make('from')
                            ->label('С')
                            ->native(false)
                            ->locale('ru')
                            ->displayFormat('d.m.Y'),
                        DatePicker::make('until')
                            ->label('По')
                            ->native(false)
                            ->locale('ru')
                            ->displayFormat('d.m.Y'),
                    ])
                    ->columns(2)
                    ->columnSpan(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                filled($data['from'] ?? null),
                                fn (Builder $query): Builder => $query->whereDate('service_date', '>=', $data['from'])
                            )
                            ->when(
                                filled($data['until'] ?? null),
                                fn (Builder $query): Builder => $query->whereDate('service_date', '<=', $data['until'])
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if (filled($data['from'] ?? null)) {
                            $indicators[] = Indicator::make('С '.Carbon::parse($data['from'])->format('d.m.Y'))
                                ->removeField('from');
                        }

                        if (filled($data['until'] ?? null)) {
                            $indicators[] = Indicator::make('По '.Carbon::parse($data['until'])->format('d.m.Y'))
                                ->removeField('until');
                        }

                        return $indicators;
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
                    ->modalHeading('Экспорт обслуживаний')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->exporter(ServiceExporter::class),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
