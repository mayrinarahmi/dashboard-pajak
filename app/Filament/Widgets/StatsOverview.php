<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pajak Pendapatan Asli Daerah', '192.1k'),
            Stat::make('Pajak Pendapatan Transfer', '242.1K'),
            Stat::make('Lain-lain PAD yang Sah', '324.4K'),
        ];
    }
}
