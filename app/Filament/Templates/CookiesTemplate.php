<?php

namespace App\Filament\Templates;

use CubeAgency\FilamentConstructor\Filament\Forms\Components\Constructor;
use CubeAgency\FilamentTemplate\FilamentTemplate;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;

class CookiesTemplate extends FilamentTemplate
{
    public function schema(): array
    {
        return [
            RichEditor::make('cookie_notice'),
            Textarea::make('cookie_notice_description'),
            Constructor::make('blocks')
                ->use(config('filament-constructor.blocks'))
        ];
    }
}
