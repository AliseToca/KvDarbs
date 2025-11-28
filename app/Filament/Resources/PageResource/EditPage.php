<?php

namespace App\Filament\Resources\PageResource;

use App\Filament\Resources\PageResource;
use App\Filament\Traits\PageFormTrait;
use CubeAgency\FilamentPageManager\Filament\Resources\PageResource\Pages\EditPage as FilamentEditPage;

class EditPage extends FilamentEditPage
{
    use PageFormTrait;

    protected static string $resource = PageResource::class;
}
