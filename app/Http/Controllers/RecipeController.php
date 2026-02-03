<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Inertia\Response;
use Inertia\Inertia;
use App\Services\PagesService;
use CubeAgency\FilamentPageManager\Models\Page;

class RecipeController extends Controller
{
    protected PagesService $pagesService;

    /**
     * Dependency Injection – lai nevajadzētu izmantot app()
     */
    public function __construct(PagesService $pagesService)
    {
        $this->pagesService = $pagesService;
    }

    /**
     * Palīgmetodes, kas izveido attiecīgās receptes lapas saiti
     */
    protected function recipeShowUrl(Recipe $recipe): string
    {
        // Aktīvā valoda
        $currentLanguage = $this->pagesService->getLanguagePage();

        // Recepte lapa konkrētajā valodā
        $page = Page::query()
            ->where('template', 'App\Filament\Templates\RecipeTemplate')
            ->where('parent_id', $currentLanguage->id)
            ->firstOrFail();

        // Izveidojam saiti ar {recipe:slug}
        return $page->getUrl('show', [
            'recipe' => $recipe->slug,
        ]);
    }

    public function index(Page $page): Response
    {
        $recipes = Recipe::select('id', 'name', 'image_src', 'slug', 'prep_time', 'cook_time')
            ->get()
            ->map(function ($recipe) {
                return [
                    'id' => $recipe->id,
                    'name' => $recipe->name,
                    'image_src' => $recipe->image_src,
                    'prep_time' => $recipe->prep_time,
                    'cook_time' => $recipe->cook_time,
                    'total_time' => $recipe->total_time,
                    'average_rating' => $recipe->average_rating,
                    'url' => $this->recipeShowUrl($recipe),
                ];
            });

        return Inertia::render('Recipe/Index', [
            'recipes' => $recipes,
        ]);
    }

    public function show(Recipe $recipe)
    {
        $recipe->load([
            'recipeProducts.product',
            'recipeProducts.unit',
            'reviews.user:id,username',
        ]);

        return Inertia::render('Recipe/Show', [
            'recipe' => $recipe,
            'url' => $this->recipeShowUrl($recipe),
        ]);
    }
}
