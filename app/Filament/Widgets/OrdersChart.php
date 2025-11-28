<?php

namespace App\Filament\Widgets;

use Filament\Support\Colors\Color;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?int $sort = 3;

    public function getHeading(): string
    {
        return 'Orders (Placeholder)'; // <-- Replace with translation or real heading
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Unpaid Orders (Placeholder)', // <-- Replace with actual label
                    'data' => [50, 30, 10], // <-- Dummy data; replace with real values
                ],
                [
                    'label' => 'Paid Orders (Placeholder)', // <-- Replace with actual label
                    'data' => [50, 70, 90], // <-- Dummy data; replace with real values
                    'borderColor' => rgba_from_color(Color::Blue[500]),
                    'backgroundColor' => rgba_from_color(Color::Blue[500], 0.2),
                ],
            ],
            'labels' => ['Month 1', 'Month 2', 'Month 3'], // <-- Replace with real labels (e.g., month names)
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
