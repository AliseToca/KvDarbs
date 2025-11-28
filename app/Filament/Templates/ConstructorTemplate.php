<?php

namespace App\Filament\Templates;

use CubeAgency\FilamentConstructor\Filament\Forms\Components\Constructor;
use CubeAgency\FilamentTemplate\FilamentTemplate;

class ConstructorTemplate extends FilamentTemplate
{
    public function schema(): array
    {
        return [
            Constructor::make('blocks')
                ->use(config('filament-constructor.blocks'))
        ];
    }
}
