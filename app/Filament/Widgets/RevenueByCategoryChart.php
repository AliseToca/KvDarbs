<?php

namespace App\Filament\Widgets;

use Filament\Support\Colors\Color;
use Filament\Widgets\ChartWidget;

class RevenueByCategoryChart extends ChartWidget
{
    protected static ?int $sort = 2;

    public function getHeading(): string
    {
        return 'Revenue by Category (Placeholder)'; // <-- Replace with translation or real heading
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Category A (Placeholder)', // <-- Replace with real category name
                    'data' => [503, 304, 454, 555], // <-- Dummy data; replace with real values
                ],
                [
                    'label' => 'Category B (Placeholder)', // <-- Replace with real category name
                    'data' => [33, 667, 888, 999], // <-- Dummy data; replace with real values
                    'pointBackgroundColor' => rgba_from_color(Color::Blue[500]),
                    'borderColor' => rgba_from_color(Color::Blue[500]),
                    'backgroundColor' => rgba_from_color(Color::Blue[500], 0.2),
                ],
            ],
            // Replace with real labels (e.g., months or categories)
            'labels' => ['Label 1', 'Label 2', 'Label 3', 'Label 4'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
