<?php

namespace App\Filament\Templates;

use CubeAgency\FilamentConstructor\Filament\Forms\Components\Constructor;
use CubeAgency\FilamentTemplate\FilamentTemplate;
use Filament\Forms\Components\Select;
use App\Repositories\LanguageRepository;

class LanguageTemplate extends FilamentTemplate
{
    public function schema(): array
    {
        return [
            Select::make('locale')
                ->options(app(LanguageRepository::class)->locales())
                ->rules('required'),
            Constructor::make('blocks')
                ->use(config('filament-constructor.language_blocks'))
        ];
    }
}
