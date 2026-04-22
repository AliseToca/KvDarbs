<?php

namespace App\Filament\Widgets;

use App\Models\RecipeCategory;
use Filament\Support\Colors\Color;
use Filament\Widgets\ChartWidget;

class RecipesByCategoryChart extends ChartWidget
{
    protected static ?int $sort = 2;

    public function getHeading(): string
    {
        return __('widgets.recipes_by_category');
    }

    protected function getData(): array
    {
        $categories = RecipeCategory::withCount('recipes')->get();

        $colors = [
            'rgba(99, 102, 241, 0.7)',  // indigo
            'rgba(16, 185, 129, 0.7)',  // emerald
            'rgba(245, 158, 11, 0.7)',  // amber
            'rgba(239, 68, 68, 0.7)',   // red
            'rgba(59, 130, 246, 0.7)',  // blue
            'rgba(168, 85, 247, 0.7)',  // purple
            'rgba(236, 72, 153, 0.7)',  // pink
            'rgba(20, 184, 166, 0.7)',  // teal
        ];

        return [
            'datasets' => [
                [
                    'label' => __('widgets.recipes'),
                    'data' => $categories->pluck('recipes_count')->toArray(),
                    'backgroundColor' => array_slice(
                        array_merge($colors, $colors),
                        0,
                        $categories->count()
                    ),
                ],
            ],
            'labels' => $categories->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
