<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RecipeCategory;
use App\Models\RecipeType;
use App\Models\Unit;
use App\Services\MeasurmentConversionService;
use App\Services\RecipeAvailabilityService;
use CubeAgency\FilamentConstructor\Filament\Forms\Components\Constructor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

    protected function loadRecipePage(): Page
    {
        $currentLanguage = $this->pagesService->getLanguagePage();

        return Page::query()
            ->select(['id', 'name', "content->blocks as blocks"])
            ->where('template', 'App\Filament\Templates\RecipeTemplate')
            ->where('parent_id', $currentLanguage->id)
            ->firstOrFail();
    }


    public function index(Request $request): Response
    {
        $page = $this->loadRecipePage();
        $user = auth()->user();

        $recipes = Recipe::select('id', 'name', 'image_src', 'slug', 'prep_time', 'cook_time')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(1)
            ->withQueryString()
            ->through(function ($recipe) use ($user) {
                $availability = RecipeAvailabilityService::calculate($recipe, $user);

                return [
                    'id' => $recipe->id,
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
            });

        return Inertia::render('Recipe/Index', [
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
        ]);
    }

    public function show(Recipe $recipe)
    {
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
                'product'=> [
                    'id' => $recipeProduct->product_id,
                    'name' => $recipeProduct->product->name,
                ],
                'unit' =>[
                    'id' => $converted['unit_id'],
                    'name' => $converted['unit'],
                ]
            ];
        });

        $reviews = $recipe->reviews()
            ->with('user:id,username')
            ->latest()
            ->paginate(5);

        $recipesPage = $this->loadRecipePage();

        return Inertia::render('Recipe/Show', [
            'recipe' => $recipe,
            'reviews' => $reviews,
            'url' => $this->recipeShowUrl($recipe),
            'breadcrumbs' => [
                [
                    'name' => $recipesPage->name,
                    'url' => $recipesPage->getUrl('index'),
                ],
                [
                    'name' => $recipe->name,
                    'url' => null, //pašreizējā lapa, nav saites
                ],
            ],
        ]);
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

        if($request->hasFile('image_src')) {
            $path = $request->file('image_src')->store('recipes', 'public');
        } else{
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

        return redirect()->to($this->recipeShowUrl($recipe));
    }
}
