<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Revenue (Placeholder)', '$0.0k') // <-- Replace with actual revenue
            ->description('X% increase') // <-- Placeholder description
            ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([4, 6, 7, 5, 10, 8, 9]) // <-- Dummy data; replace with actual data points
                ->color('success'),

            Stat::make('New customers (Placeholder)', '123') // <-- Replace with actual count
            ->description('X% decrease') // <-- Placeholder description
            ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('New orders (Placeholder)', '0') // <-- Replace with actual count
            ->description('X% increase') // <-- Placeholder description
            ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
