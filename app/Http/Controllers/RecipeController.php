<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;
use App\Services\PagesService;
use App\Models\Recipe;
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

    public function index(Request $request): Response
    {
        $recipes = Recipe::select('id', 'name', 'image_src', 'slug', 'prep_time', 'cook_time')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(1)
            ->withQueryString()
            ->through(function ($recipe) {
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
            'filters' => [
                'search' => $request->search,
            ]
        ]);
    }

    public function show(Recipe $recipe)
    {
        $recipe->load([
            'recipeProducts.product',
            'recipeProducts.unit',
        ]);

        $reviews = $recipe->reviews()->with('user:id,username')->latest()->paginate(5);

        return Inertia::render('Recipe/Show', [
            'recipe' => $recipe,
            'reviews' => $reviews,
            'url' => $this->recipeShowUrl($recipe),
        ]);
    }
}
