<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\User;
use App\Services\BreadcrumbService;
use App\Services\PagesService;
use App\Services\RecipeAvailabilityService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\Rule;

class FolderController extends Controller
{
    use AuthorizesRequests;

    public function __construct(BreadcrumbService $breadcrumbService, PagesService $pagesService, RecipeAvailabilityService $recipeAvailabilityService)
    {
        $this->breadcrumbService = $breadcrumbService;
        $this->pagesService = $pagesService;
        $this->recipeAvailabilityService = $recipeAvailabilityService;
    }

    public function index(Request $request): Response
    {
        $folders = $request->user()->folders()->with(['recipes', 'user:id,username'])->latest()->paginate(12);

        return Inertia::render('Folder/Index', [
            'folders' => $folders,
        ]);
    }

    public function show(User $user, Folder $folder): Response
    {
        $this->authorize('view', $user, $folder);

        $folder->load(['recipes.recipeProducts.product.measurementType.units']);

        $authUser = auth()->user();
        $page = $this->pagesService->getRecipeIndexPage();

        $folder->recipes->transform(function ($recipe) use ($authUser, $page) {
            $availability = RecipeAvailabilityService::calculate($recipe, $authUser);

            return [
                ...$recipe->toArray(),
                'url' => $page->getUrl('show', ['recipe' => $recipe->slug]),
                'missing_products_count' => $availability['missing_products_count'],
                'available_products_count' => $availability['available_products_count'],
                'total_products_count' => $availability['total_products_count'],
                'compatibility' => $availability['compatibility'],
            ];
        });

        return Inertia::render('Folder/Show', [
            'folder' => $folder,
            'breadcrumbs' => $this->breadcrumbService->forFolderShow($user, $folder),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('folders')->where('user_id', $request->user()->id),
            ],
        ]);

        $request->user()->folders()->create($validated);

        return redirect()->back()->with('success', 'Recepšu saraksts veiksmīgi izveidots');
    }

    public function update(Request $request, Folder $folder): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
        ]);

        $folder->update($validated);

        return redirect()->route('folders.show', [
            'user' => $folder->user->username,
            'folder' => $folder->fresh(),
        ])->with('success', 'Veiksmīgi atjaunināta saraksta informācija');
    }

    public function destroy(Folder $folder): RedirectResponse
    {
        $this->authorize('delete', $folder);

        $folder->delete();

        return redirect()->route('folders.index')->with('success', 'Veiksmīgi dzēsts saraksts');
    }
}
