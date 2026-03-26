<?php

namespace App\Services;

use App\Services\PagesService;
use App\Services\HouseholdUrlService;
use App\Models\Household;
use App\Models\Recipe;

class BreadcrumbService
{
    public function __construct(PagesService $pagesService, HouseholdUrlService $householdUrlService)
    {
        $this->pagesService = $pagesService;
        $this->householdUrlService = $householdUrlService;
    }

    public function forRecipe(Recipe $recipe): array
    {
        $recipesPage = $this->pagesService->getRecipeIndexPage();

        return [
            ['name' => $recipesPage->name, 'url' => $recipesPage->getUrl('index')],
            ['name' => $recipe->name, 'url' => null],
        ];
    }

    public function forRecipeCreate(): array
    {
        return [
            ['name' => trans('recipe.my_recipes.title'), 'url' => route('recipe.my')],
            ['name' => trans('recipe.create_recipe'), 'url' => null],
        ];
    }

    public function forRecipeEdit(Recipe $recipe): array
    {
        return [
            ['name' => trans('recipe.my_recipes.title'), 'url' => route('recipe.my')],
            ['name' => trans('button.edit') . ' "' . $recipe->name . '"', 'url' => null],
        ];
    }

    public function forHouseholdEdit(Household $household): array
    {
        $owner = $household->users()->wherePivot('role', 'owner')->first();

        return [
            ['name' => $household->name, 'url' => $this->householdUrlService->showUrl($owner)],
            ['name' => trans('household.edit'), 'url' => null],
        ];
    }
}
