<?php

namespace App\Filament\Constructor\Blocks;

use CubeAgency\FilamentConstructor\Constructor\Blocks\BlockRenderer;
use Filament\Forms\Components\Textarea;

class SimpleTextBlock extends BlockRenderer
{
    public function name(): string
    {
        return 'simple_text';
    }

    public function title(): string
    {
        return __('Text');
    }

    public function schema(): array
    {
        return [
            Textarea::make('text'),
        ];
    }
}
