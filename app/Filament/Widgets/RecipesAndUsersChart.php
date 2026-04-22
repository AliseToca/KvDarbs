<?php

namespace App\Filament\Widgets;

use App\Models\Recipe;
use App\Models\User;
use Filament\Support\Colors\Color;
use Filament\Widgets\ChartWidget;

class RecipesAndUsersChart extends ChartWidget
{
    protected static ?int $sort = 3;

    public function getHeading(): string
    {
        return __('widgets.recipes_and_users_per_month');
    }

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i));

        $recipeData = $months->map(fn ($month) =>
        Recipe::whereMonth('created_at', $month->month)
            ->whereYear('created_at', $month->year)
            ->count()
        )->toArray();

        $userData = $months->map(fn ($month) =>
        User::whereMonth('created_at', $month->month)
            ->whereYear('created_at', $month->year)
            ->count()
        )->toArray();

        $labels = $months->map(fn ($month) => $month->format('M Y'))->toArray();

        return [
            'datasets' => [
                [
                    'label' => __('widgets.recipes'),
                    'data' => $recipeData,
                    'borderColor' => rgba_from_color(Color::Green[500]),
                    'backgroundColor' => rgba_from_color(Color::Green[500], 0.2),
                ],
                [
                    'label' => __('widgets.users'),
                    'data' => $userData,
                    'borderColor' => rgba_from_color(Color::Blue[500]),
                    'backgroundColor' => rgba_from_color(Color::Blue[500], 0.2),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
