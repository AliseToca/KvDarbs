<?php

namespace App\Filament\Resources\RecipeTypeResource\Pages;

use App\Filament\Resources\RecipeTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecipeTypes extends ListRecords
{
    protected static string $resource = RecipeTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
