<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RecipeCategory;
use App\Models\RecipeType;
use App\Models\Unit;
use App\Services\MeasurmentConversionService;
use App\Services\RecipeAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Response;
use Inertia\Inertia;
use App\Services\PagesService;
use App\Models\Recipe;
use App\Services\BreadcrumbService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RecipeController extends Controller
{
    use AuthorizesRequests;

    protected PagesService $pagesService;
    protected BreadcrumbService $breadcrumbService;

    /**
     * Dependency Injection – lai nevajadzētu izmantot app()
     */
    public function __construct(PagesService $pagesService, BreadcrumbService $breadcrumbService)
    {
        $this->pagesService = $pagesService;
        $this->breadcrumbService = $breadcrumbService;
    }

    /**
     * Palīgmetodes, kas izveido attiecīgās receptes lapas saiti
     */
    protected function recipeShowUrl(Recipe $recipe): string
    {
        $page = $this->pagesService->getRecipeIndexPage();

        return $page->getUrl('show', [
            'recipe' => $recipe->slug,
        ]);
    }

    /**
     * Maps a recipe to an array for the frontend
     */
    private function mapRecipe(Recipe $recipe, $user): array
    {
        $availability = RecipeAvailabilityService::calculate($recipe, $user);

        return [
            'id' => $recipe->id,
            'slug' => $recipe->slug,
            'name' => $recipe->name,
            'image_src' => $recipe->image_src,
            'prep_time' => $recipe->prep_time,
            'cook_time' => $recipe->cook_time,
            'total_time' => $recipe->total_time,
            'average_rating' => $recipe->average_rating,
            'missing_products_count' => $availability['missing_products_count'],
            'compatibility' => $availability['compatibility'],
            'url' => $this->recipeShowUrl($recipe),
        ];
    }

    public function index(Request $request): Response
    {
        $page = $this->pagesService->getRecipeIndexPage();
        $user = auth()->user();

        $recipes = Recipe::select('id', 'name', 'image_src', 'slug', 'prep_time', 'cook_time')
            ->visibleTo($user)
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->paginate(12)
            ->withQueryString()
            ->through(fn($recipe) => $this->mapRecipe($recipe, $user));

        return Inertia::render('Recipe/Index', [
            'page_name' => $page->name,
            'blocks' => json_decode($page->blocks) ?? [],
            'recipes' => $recipes,
            'filters' => [
                'search' => $request->search,
            ]
        ]);
    }

    public function myRecipes(Request $request): Response
    {
        $page = $this->pagesService->getRecipeIndexPage();
        $user = auth()->user();

        $recipes = Recipe::select('id', 'name', 'image_src', 'slug', 'prep_time', 'cook_time')
            ->where('user_id', $user->id)
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->paginate(12)
            ->withQueryString()
            ->through(fn($recipe) => $this->mapRecipe($recipe, $user));

        return Inertia::render('Recipe/MyRecipes', [
            'page_name' => $page->name,
            'blocks' => json_decode($page->blocks) ?? [],
            'recipes' => $recipes,
            'filters' => [
                'search' => $request->search,
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Recipe/Create', [
            'categories' => RecipeCategory::all(),
            'types' => RecipeType::all(),
            'products' => Product::all(),
            'units' => Unit::all(),
            'breadcrumbs' => [
                ['name' => trans('recipe.my_recipes.title'), 'url' => route('recipe.my')],
                ['name' => trans('recipe.create_recipe'), 'url' => null],
            ],
        ]);
    }

    public function show(Recipe $recipe)
    {
        $this->authorize('view', $recipe);

        $recipe->load([
            'user:id,username',
            'recipeProducts.product.measurementType.units',
        ]);

        $recipe->recipeProducts->transform(function ($recipeProduct) {
            $converted = MeasurmentConversionService::fromBaseAmount(
                $recipeProduct->amount,
                $recipeProduct->product
            );

            return [
                'id' => $recipeProduct->id,
                'amount' => $converted['amount'],
                'product' => [
                    'id' => $recipeProduct->product_id,
                    'name' => $recipeProduct->product->name,
                ],
                'unit' => [
                    'id' => $converted['unit_id'],
                    'name' => $converted['unit'],
                ]
            ];
        });

        $reviews = $recipe->reviews()
            ->with('user:id,username')
            ->latest()
            ->paginate(5);

        return Inertia::render('Recipe/Show', [
            'recipe' => $recipe,
            'reviews' => $reviews,
            'url' => $this->recipeShowUrl($recipe),
            'breadcrumbs' => $this->breadcrumbService->getBreadcrumbs($recipe),
        ]);
    }

    public function edit(Recipe $recipe)
    {
        $this->authorize('update', $recipe);

        $recipe->load(['recipeProducts.product.measurementType.units']);

        $recipe->recipeProducts->transform(function ($recipeProduct) {
            $converted = MeasurmentConversionService::fromBaseAmount(
                $recipeProduct->amount,
                $recipeProduct->product
            );

            return [
                'id' => $recipeProduct->id,
                'amount' => $converted['amount'],
                'product' => [
                    'id' => $recipeProduct->product_id,
                    'name' => $recipeProduct->product->name,
                ],
                'unit' => [
                    'id' => $converted['unit_id'],
                    'name' => $converted['unit'],
                ],
            ];
        });

        return Inertia::render('Recipe/Edit', [
            'recipe' => $recipe,
            'products' => Product::all(),
            'units' => Unit::all(),
            'breadcrumbs' => [
                ['name' => trans('recipe.my_recipes.title'), 'url' => route('recipe.my')],
                ['name' => trans('button.edit') . ' "' . $recipe->name . '"' , 'url' => null],
            ],
        ]);
    }

    public function update(Request $request, Recipe $recipe)
    {
        $this->authorize('update', $recipe);

        $data = $request->validate([
            'name' => 'required|string',
            'image_src' => 'nullable|image|max:2048',
            'visibility' => 'required|string',
            'prep_time' => 'required|numeric',
            'cook_time' => 'required|numeric',
            'servings' => 'required|numeric',
            'instructions' => 'required|array',
            'instructions.*' => 'required|string',
            'recipe_products' => 'required|array',
            'recipe_products.*.product_id' => 'required|numeric',
            'recipe_products.*.amount' => 'required|numeric|min:0',
            'recipe_products.*.unit_id' => 'required|numeric',
        ]);

        if ($request->hasFile('image_src')) {
            if ($recipe->image_src) {
                Storage::disk('public')->delete($recipe->image_src);
            }
            $path = $request->file('image_src')->store('recipes', 'public');
        } else {
            $path = $recipe->image_src;
        }

        $recipe->update([
            'name' => $data['name'],
            'image_src' => $path,
            'visibility' => $data['visibility'],
            'prep_time' => $data['prep_time'],
            'cook_time' => $data['cook_time'],
            'servings' => $data['servings'],
            'instructions' => $data['instructions'],
        ]);

        $recipe->recipeProducts()->delete();

        foreach ($data['recipe_products'] as $recipeProduct) {
            $unit = Unit::findOrFail($recipeProduct['unit_id']);
            $product = Product::findOrFail($recipeProduct['product_id']);
            $amountInBase = MeasurmentConversionService::toBaseAmount($recipeProduct['amount'], $unit, $product);

            $recipe->recipeProducts()->create([
                'product_id' => $recipeProduct['product_id'],
                'amount' => $amountInBase,
            ]);
        }

        return redirect()->to($this->recipeShowUrl($recipe))->with('success', 'Recepte ir atjaunināta');
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'image_src' => 'nullable|image|max:2048',
            'visibility' => 'required|string',
            'prep_time' => 'required|numeric',
            'cook_time' => 'required|numeric',
            'servings' => 'required|numeric',

            'instructions' => 'required|array',
            'instructions.*' => 'required|string',

            'recipe_products' => 'required|array',
            'recipe_products.*.product_id' => 'required|numeric',
            'recipe_products.*.amount' => 'required|numeric|min:0',
            'recipe_products.*.unit_id' => 'required|numeric',
        ]);

        if ($request->hasFile('image_src')) {
            $path = $request->file('image_src')->store('recipes', 'public');
        } else {
            $path = null;
        }

        $user = auth()->user();
        $slug = $user->username . '-' . Str::slug($data['name']);

        $recipe = Recipe::create([
            'name' => $data['name'],
            'image_src' => $path,
            'slug' => $slug,
            'content' => 'test',
            'visibility' => $data['visibility'],
            'prep_time' => $data['prep_time'],
            'cook_time' => $data['cook_time'],
            'servings' => $data['servings'],
            'instructions' => $data['instructions'],
            'user_id' => $user->id,
        ]);

        foreach ($data['recipe_products'] as $recipeProduct) {
            $unit = Unit::findOrFail($recipeProduct['unit_id']);
            $product = Product::findOrFail($recipeProduct['product_id']);

            $amountInBase = MeasurmentConversionService::toBaseAmount($recipeProduct['amount'], $unit, $product);

            $recipe->recipeProducts()->create([
                'product_id' => $recipeProduct['product_id'],
                'amount' => $amountInBase,
            ]);
        }

        return redirect()->to($this->recipeShowUrl($recipe))->with('success', 'Recepte ir izveidota');
    }

    public function destroy(Recipe $recipe)
    {
        $this->authorize('delete', $recipe);

        if($recipe->image_src) {
            Storage::disk('public')->delete($recipe->image_src);
        }

        $recipe->recipeProducts()->delete();
        $recipe->delete();

        return redirect()->route('recipe.my')->with('success', 'Recepte ir dzēsta');
    }

}
