<?php

namespace App\Filament\Widgets;

use App\Models\Plant;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())->icon('heroicon-o-user-group'),
            Stat::make('Total Tanaman', Plant::count()),
        ];
    }
}
