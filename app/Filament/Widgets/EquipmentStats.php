<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Service;
use App\Models\Supply;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EquipmentStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $equipmentCount = Equipment::query()->count();
        $assignedCount = Equipment::query()->whereNotNull('id_employee')->count();
        $employeeCount = Employee::query()->count();
        $serviceCount = Service::query()->count();
        $lowStockCount = Supply::query()->where('quantity', '<=', 10)->count();

        return [
            Stat::make('Оборудование', $equipmentCount)
                ->description("Выдано сотрудникам: {$assignedCount}")
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->icon(Heroicon::OutlinedComputerDesktop)
                ->color('primary'),

            Stat::make('Сотрудники', $employeeCount)
                ->description('Активные пользователи')
                ->descriptionIcon(Heroicon::OutlinedUsers)
                ->icon(Heroicon::OutlinedUserGroup)
                ->color('success'),

            Stat::make('Обслуживания', $serviceCount)
                ->description('Всего записей')
                ->descriptionIcon(Heroicon::OutlinedWrenchScrewdriver)
                ->icon(Heroicon::OutlinedWrenchScrewdriver)
                ->color('info'),

            Stat::make('Низкий остаток', $lowStockCount)
                ->description('Расходники с остатком 10 и меньше')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->icon(Heroicon::OutlinedArchiveBox)
                ->color($lowStockCount > 0 ? 'danger' : 'success'),
        ];
    }
}
