<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use CubeAgency\FilamentPageManager\Models\Page;
use Inertia\Response;
use Inertia\Inertia;
class RecipeController extends Controller
{
    public function index(Page $page): Response
    {
        return Inertia::render('Recipe/Index', [
            'recipes' => Recipe::select('name','image_src', 'slug', 'prep_time', 'cook_time')
                ->get()
                ->append('url'),
        ]);
    }

    public function show(Recipe $recipe)
    {
        $recipe->load(['recipeProducts.product', 'recipeProducts.unit']);

        return Inertia::render('Recipe/Show', [
            'recipe' => $recipe
        ]);
    }
}
