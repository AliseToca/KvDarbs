<?php

namespace App\Services;

use App\Models\Recipe;
use App\Services\PagesService;

class BreadcrumbService
{
    protected PagesService $pagesService;

    public function __construct(PagesService $pagesService)
    {
        $this->pagesService = $pagesService;
    }

    public function getBreadcrumbs(Recipe $recipe): array
    {
        $recipesPage = $this->pagesService->getRecipeIndexPage();

        return [
            [
                'name' => $recipesPage->name,
                'url' => $recipesPage->getUrl('index'),
            ],
            [
                'name' => $recipe->name,
                'url' => null,
            ],
        ];
    }
}

