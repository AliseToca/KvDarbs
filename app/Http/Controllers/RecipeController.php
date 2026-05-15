<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RecipeCategory;
use App\Models\RecipeType;
use App\Models\Unit;
use App\Services\MeasurmentConversionService;
use App\Services\RecipeAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Inertia\Response;
use Inertia\Inertia;
use App\Services\PagesService;
use App\Models\Recipe;
use App\Services\BreadcrumbService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RecipeController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected PagesService $pagesService,
        protected BreadcrumbService $breadcrumbService,
    ) {}

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Builds the public-facing URL for a given recipe's show page.
     */
    protected function recipeShowUrl(Recipe $recipe): string
    {
        $page = $this->pagesService->getRecipeIndexPage();

        return $page->getUrl('show', [
            'recipe' => $recipe->slug,
        ]);
    }

    /**
     * Maps a Recipe model to a flat array suitable for the frontend,
     * including availability stats relative to the authenticated user.
     */
    private function mapRecipe(Recipe $recipe, $user): array
    {
        $availability = RecipeAvailabilityService::calculate($recipe, $user);

        return [
            'id'                       => $recipe->id,
            'slug'                     => $recipe->slug,
            'name'                     => $recipe->name,
            'image_src'                => $recipe->image_src,
            'prep_time'                => $recipe->prep_time,
            'cook_time'                => $recipe->cook_time,
            'total_time'               => $recipe->total_time,
            'servings'                 => $recipe->servings,
            'average_rating'           => $recipe->average_rating,
            'reviews_count'            => $recipe->reviewsCount,
            'missing_products_count'   => $availability['missing_products_count'],
            'available_products_count' => $availability['available_products_count'],
            'total_products_count'     => $availability['total_products_count'],
            'compatibility'            => $availability['compatibility'],
            'url'                      => $this->recipeShowUrl($recipe),
        ];
    }

    /**
     * Loads and maps the authenticated user's folders with a thumbnail
     * (first recipe image) and a recipe count.
     */
    private function mapFolders($user): array
    {
        return $user->folders()
            ->with(['recipes:id,image_src'])
            ->get()
            ->map(fn($folder) => [
                'id'           => $folder->id,
                'name'         => $folder->name,
                'thumbnail'    => $folder->recipes->first()?->image_src,
                'recipe_count' => $folder->recipes->count(),
            ])
            ->toArray();
    }

    /**
     * Applies a named sort order to the query and paginates the result (12 per page).
     *
     * Supported sort values: 'highest_rated', 'most_reviewed', 'quickest', default → newest.
     */
    private function applySorting($query, string $sort, Request $request)
    {
        $this->applySortOrder($query, $sort);

        return $query->paginate(12)->withQueryString();
    }

    /**
     * Applies a named sort order to the query without paginating.
     * Used when in-memory filtering (e.g. availability) must happen before slicing.
     */
    private function applySortingWithoutPaginate($query, string $sort)
    {
        return $this->applySortOrder($query, $sort);
    }

    /**
     * Mutates the query in-place with the requested sort clause and returns it.
     * Extracted to avoid duplicating the match expression across the two sorting helpers.
     */
    private function applySortOrder($query, string $sort)
    {
        match ($sort) {
            'highest_rated' => $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
            'most_reviewed' => $query->withCount('reviews')->orderByDesc('reviews_count'),
            'quickest'      => $query->orderByRaw('(prep_time + cook_time) ASC'),
            default         => $query->latest(),
        };

        return $query;
    }

    /**
     * Transforms a recipe_products collection in-place, converting each
     * product's stored base amount back to a human-readable unit.
     *
     * Shared between show() and edit() to avoid duplication.
     */
    private function transformRecipeProducts($recipeProducts): void
    {
        $recipeProducts->transform(function ($recipeProduct) {
            $converted = MeasurmentConversionService::fromBaseAmount(
                $recipeProduct->amount,
                $recipeProduct->product
            );

            return [
                'id'      => $recipeProduct->id,
                'amount'  => $converted['amount'],
                'product' => [
                    'id'   => $recipeProduct->product_id,
                    'name' => $recipeProduct->product->name,
                ],
                'unit'    => [
                    'id'   => $converted['unit_id'],
                    'name' => $converted['unit'],
                ],
            ];
        });
    }

    /**
     * Stores an uploaded recipe image and removes the old file when replacing.
     * Returns the new storage path, or $currentPath when no new file was uploaded.
     */
    private function handleImageUpload(Request $request, ?string $currentPath): ?string
    {
        if (!$request->hasFile('image_src')) {
            return $currentPath;
        }

        // Remove the previous image to avoid orphaned files on disk.
        if ($currentPath) {
            Storage::disk('public')->delete($currentPath);
        }

        return $request->file('image_src')->store('recipes', 'public');
    }

    /**
     * Replaces all recipe_products for a recipe using the validated input.
     * Converts each product amount from the user-selected unit to the base unit.
     */
    private function syncRecipeProducts(Recipe $recipe, array $recipeProductsData): void
    {
        // Delete existing products before re-inserting to keep the relation clean.
        $recipe->recipeProducts()->delete();

        foreach ($recipeProductsData as $item) {
            $unit    = Unit::findOrFail($item['unit_id']);
            $product = Product::findOrFail($item['product_id']);

            $amountInBase = MeasurmentConversionService::toBaseAmount($item['amount'], $unit, $product);

            $recipe->recipeProducts()->create([
                'product_id' => $item['product_id'],
                'amount'     => $amountInBase,
            ]);
        }
    }

    /**
     * Validation rules shared between store() and update().
     * The image rule changes depending on whether a new file is being uploaded.
     */
    private function recipeValidationRules(Request $request): array
    {
        return [
            'name'                              => 'required|string',
            'image_src'                         => $request->hasFile('image_src')
                ? 'nullable|image|max:2048'
                : 'nullable',
            'visibility'                        => 'required|string',
            'prep_time'                         => 'required|numeric',
            'cook_time'                         => 'required|numeric',
            'servings'                          => 'required|numeric',
            'instructions'                      => 'required|array',
            'instructions.*'                    => 'required|string',
            'recipe_products'                   => 'required|array',
            'recipe_products.*.product_id'      => 'required|numeric',
            'recipe_products.*.amount'          => 'required|numeric|min:0',
            'recipe_products.*.unit_id'         => 'required|numeric',
            'recipe_type_id'                    => 'nullable|exists:recipe_types,id',
            'recipe_category_ids'               => 'nullable|array',
            'recipe_category_ids.*'             => 'exists:recipe_categories,id',
        ];
    }

    // -------------------------------------------------------------------------
    // Public actions (CRUD + extras)
    // -------------------------------------------------------------------------

    /**
     * Displays the public recipe listing with filtering, sorting, and optional
     * "available ingredients only" mode that requires in-memory post-filtering.
     */
    public function index(Request $request): Response
    {
        $page = $this->pagesService->getRecipeIndexPage();
        $user = auth()->user();

        $sort          = $request->get('sort', 'newest');
        $availableOnly = $request->boolean('available') && $user;
        $categoryIds   = array_filter(explode(',', $request->get('categories', '')));

        // Base query: only columns needed by mapRecipe() to keep the payload lean.
        $query = Recipe::select('id', 'name', 'image_src', 'slug', 'prep_time', 'cook_time', 'servings')
            ->visibleTo($user)
            ->when($request->search,   fn($q, $s)  => $q->where('name', 'like', "%{$s}%"))
            ->when($request->type,     fn($q, $id) => $q->where('recipe_type_id', $id))
            ->when($categoryIds, function ($q) use ($categoryIds) {
                // Each category must match (AND logic), so we add a whereHas per ID.
                foreach ($categoryIds as $categoryId) {
                    $q->whereHas('recipeCategories', fn($q) => $q->where('recipe_categories.id', $categoryId));
                }
            });

        if ($availableOnly) {
            // Availability is computed in PHP, so we must fetch all matching records
            // first, filter them, then manually paginate the result.
            $perPage     = 12;
            $currentPage = $request->get('page', 1);

            $allRecipes = $this->applySortingWithoutPaginate($query, $sort)
                ->get()
                ->map(fn($recipe) => $this->mapRecipe($recipe, $user))
                ->filter(fn($recipe) => $recipe['compatibility'] == 100)
                ->values();

            $recipes = new LengthAwarePaginator(
                $allRecipes->forPage($currentPage, $perPage),
                $allRecipes->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // Standard DB-level sort + paginate path.
            $recipes = $this->applySorting($query, $sort, $request)
                ->through(fn($recipe) => $this->mapRecipe($recipe, $user));
        }

        return Inertia::render('Recipe/Index', [
            'page_name'  => $page->name,
            'blocks'     => json_decode($page->blocks) ?? [],
            'recipes'    => $recipes,
            'folders'    => $this->mapFolders($user),
            'categories' => RecipeCategory::all(),
            'types'      => RecipeType::all(),
            'filters'    => [
                'search'     => $request->search,
                'sort'       => $sort,
                'categories' => $request->get('categories', ''),
                'available'  => $availableOnly,
            ],
        ]);
    }

    /**
     * Displays only the recipes created by the authenticated user.
     */
    public function myRecipes(Request $request): Response
    {
        $page = $this->pagesService->getRecipeIndexPage();
        $user = auth()->user();

        // Separate base query so we can reuse it for the total count without pagination.
        $baseQuery = Recipe::where('user_id', $user->id);

        $recipes = $baseQuery
            ->select('id', 'name', 'image_src', 'slug', 'prep_time', 'cook_time', 'servings')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->paginate(12)
            ->withQueryString()
            ->through(fn($recipe) => $this->mapRecipe($recipe, $user));

        return Inertia::render('Recipe/MyRecipes', [
            'page_name'    => $page->name,
            'blocks'       => json_decode($page->blocks) ?? [],
            'recipes'      => $recipes,
            'recipe_count' => $baseQuery->count(),
            'filters'      => ['search' => $request->search],
            'folders'      => $this->mapFolders($user),
        ]);
    }

    /**
     * Renders the recipe creation form with all supporting lookups.
     */
    public function create(): Response
    {
        return Inertia::render('Recipe/Create', [
            'categories'  => RecipeCategory::all(),
            'types'       => RecipeType::all(),
            'products'    => Product::all(),
            'units'       => Unit::all(),
            'breadcrumbs' => $this->breadcrumbService->forRecipeCreate(),
        ]);
    }

    /**
     * Displays a single recipe.
     * Authorization is enforced via RecipePolicy@view.
     */
    public function show(Request $request, Recipe $recipe): Response
    {
        $this->authorize('view', $recipe);

        $recipe->load([
            'user:id,username,avatar_src',
            'recipeProducts.product.measurementType.units',
            'recipeType',
            'recipeCategories',
        ]);

        // Convert stored base amounts to display units for each ingredient.
        $this->transformRecipeProducts($recipe->recipeProducts);

        $reviews = $recipe->reviews()
            ->with('user:id,username,avatar_src')
            ->latest()
            ->paginate(5);

        $user       = auth()->user();
        $fromFolder = $request->query('from_folder')
            ? $user->folders()->find($request->query('from_folder'))
            : null;

        return Inertia::render('Recipe/Show', [
            'recipe'      => $recipe,
            'reviews'     => $reviews,
            'url'         => $this->recipeShowUrl($recipe),
            'breadcrumbs' => $fromFolder
                ? $this->breadcrumbService->forRecipeFromFolder($recipe, $fromFolder)
                : $this->breadcrumbService->forRecipe($recipe),
            'folders'     => $this->mapFolders($user),
        ]);
    }

    /**
     * Renders the recipe edit form pre-populated with existing data.
     * Authorization is enforced via RecipePolicy@update.
     */
    public function edit(Recipe $recipe): Response
    {
        $this->authorize('update', $recipe);

        $recipe->load([
            'recipeProducts.product.measurementType.units',
            'recipeType',
            'recipeCategories',
        ]);

        // Convert stored base amounts back to human-readable units for the form.
        $this->transformRecipeProducts($recipe->recipeProducts);

        return Inertia::render('Recipe/Edit', [
            'recipe'      => $recipe,
            'products'    => Product::all(),
            'units'       => Unit::all(),
            'types'       => RecipeType::all(),
            'categories'  => RecipeCategory::all(),
            'breadcrumbs' => $this->breadcrumbService->forRecipeEdit($recipe),
        ]);
    }

    /**
     * Persists updates to an existing recipe.
     * Authorization is enforced via RecipePolicy@update.
     */
    public function update(Request $request, Recipe $recipe)
    {
        $this->authorize('update', $recipe);

        $data = $request->validate($this->recipeValidationRules($request));

        $imagePath = $this->handleImageUpload($request, $recipe->image_src);

        $recipe->update([
            'name'           => $data['name'],
            'image_src'      => $imagePath,
            'visibility'     => $data['visibility'],
            'prep_time'      => $data['prep_time'],
            'cook_time'      => $data['cook_time'],
            'servings'       => $data['servings'],
            'instructions'   => $data['instructions'],
            'recipe_type_id' => $data['recipe_type_id'] ?? null,
        ]);

        $recipe->recipeCategories()->sync($data['recipe_category_ids'] ?? []);

        $this->syncRecipeProducts($recipe, $data['recipe_products']);

        return redirect()
            ->to($this->recipeShowUrl($recipe))
            ->with('success', 'Recepte informācija veiksmīgi atjaunināta');
    }

    /**
     * Creates and persists a new recipe owned by the authenticated user.
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->recipeValidationRules($request));

        $imagePath = $this->handleImageUpload($request, null);

        $user = auth()->user();

        // Slug combines the username with a URL-safe version of the recipe name.
        $slug = $user->username . '-' . Str::slug($data['name']);

        $recipe = Recipe::create([
            'name'           => $data['name'],
            'image_src'      => $imagePath,
            'slug'           => $slug,
            'visibility'     => $data['visibility'],
            'prep_time'      => $data['prep_time'],
            'cook_time'      => $data['cook_time'],
            'servings'       => $data['servings'],
            'instructions'   => $data['instructions'],
            'user_id'        => $user->id,
            'recipe_type_id' => $data['recipe_type_id'] ?? null,
        ]);

        $recipe->recipeCategories()->sync($data['recipe_category_ids'] ?? []);

        $this->syncRecipeProducts($recipe, $data['recipe_products']);

        return redirect()
            ->to($this->recipeShowUrl($recipe))
            ->with('success', 'Recepte ir veiksmīgi izveidota');
    }

    /**
     * Deletes a recipe along with its image and related products.
     * Authorization is enforced via RecipePolicy@delete.
     */
    public function destroy(Recipe $recipe)
    {
        $this->authorize('delete', $recipe);

        // Remove the stored image file to avoid orphaned files on disk.
        if ($recipe->image_src) {
            Storage::disk('public')->delete($recipe->image_src);
        }

        $recipe->recipeProducts()->delete();
        $recipe->delete();

        return redirect()
            ->route('recipe.my')
            ->with('success', 'Recepte ir veiksmīgi dzēsta');
    }
}
