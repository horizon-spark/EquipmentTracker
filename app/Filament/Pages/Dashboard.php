<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Обзор';

    protected static ?string $title = 'Обзор';

    public function getSubheading(): ?string
    {
        return 'Сводка по парку ИТ-оборудования';
    }
}
