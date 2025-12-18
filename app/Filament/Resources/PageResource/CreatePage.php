<?php

namespace App\Filament\Resources\PageResource;

use App\Filament\Resources\PageResource;
use CubeAgency\FilamentPageManager\Filament\Resources\PageResource\Pages\CreatePage as FilamentCreatePage;
use CubeAgency\FilamentPageManager\Traits\PageFormTrait;

class CreatePage extends FilamentCreatePage
{
    use PageFormTrait;


    protected static string $resource = PageResource::class;
}
