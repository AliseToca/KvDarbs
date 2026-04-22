<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Recipe;
use App\Models\User;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $recipesThisMonth = Recipe::whereMonth('created_at', now()->month)->count();
        $recipesLastMonth = Recipe::whereMonth('created_at', now()->subMonth()->month)->count();
        $recipesGrowth = $recipesLastMonth > 0
            ? round((($recipesThisMonth - $recipesLastMonth) / $recipesLastMonth) * 100, 1)
            : 0;

        $usersThisMonth = User::whereMonth('created_at', now()->month)->count();
        $usersLastMonth = User::whereMonth('created_at', now()->subMonth()->month)->count();
        $usersGrowth = $usersLastMonth > 0
            ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100, 1)
            : 0;

        $recipesPerUser = User::count() > 0
            ? round(Recipe::count() / User::count(), 1)
            : 0;

        // Chart data - recipes created per month for last 7 months
        $chartData = collect(range(6, 0))->map(fn($i) => Recipe::whereMonth('created_at', now()->subMonths($i)->month)
            ->whereYear('created_at', now()->subMonths($i)->year)
            ->count()
        )->toArray();

        return [
            Stat::make(__('widgets.recipes_this_month'), $recipesThisMonth)
                ->description($recipesGrowth >= 0
                    ? $recipesGrowth . '% increase'
                    : abs($recipesGrowth) . '% decrease')
                ->descriptionIcon($recipesGrowth >= 0
                    ? 'heroicon-m-arrow-trending-up'
                    : 'heroicon-m-arrow-trending-down')
                ->chart($chartData)
                ->color($recipesGrowth >= 0 ? 'success' : 'danger'),

            Stat::make(__('widgets.users_this_month'), $usersThisMonth)
                ->description($usersGrowth >= 0
                    ? $usersGrowth . '% increase'
                    : abs($usersGrowth) . '% decrease')
                ->descriptionIcon($usersGrowth >= 0
                    ? 'heroicon-m-arrow-trending-up'
                    : 'heroicon-m-arrow-trending-down')
                ->color($usersGrowth >= 0 ? 'success' : 'danger'),

            Stat::make(__('widgets.recipes_per_user'), $recipesPerUser)
                ->description(Recipe::count() . ' total / ' . User::count() . ' users')
                ->descriptionIcon('heroicon-m-user')
                ->color('info'),
        ];
    }
}
