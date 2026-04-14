<?php

namespace App\Http\Controllers;

use App\Filament\Templates\ShoppingListTemplate;
use App\Models\Recipe;
use App\Models\ShoppingList;
use App\Models\Page;
use App\Services\MeasurmentConversionService;
use App\Services\PagesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShoppingListController extends Controller
{
    protected PagesService $pagesService;

    public function __construct(PagesService $pagesService)
    {
        $this->pagesService = $pagesService;
    }

    public function show(): Response
    {
        $household = auth()->user()->activeHousehold();

        $shoppingList = $household?->shoppingList()->get();

        $page_name = Page::where('template', ShoppingListTemplate::class)->first()->name;

        return Inertia::render('ShoppingList/Show', [
            'page_name' => $page_name,
            'shopping_list' => $shoppingList,
            'household' => [
                'name' => $household?->name ?? '',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:40']);

        $household = auth()->user()->activeHousehold();

        $household->shoppingList()->create([
            'name' => $request->name,
        ]);

        return back();
    }

    public function toggle(ShoppingList $shoppingList): RedirectResponse
    {
        $shoppingList->update(['is_checked' => !$shoppingList->is_checked]);

        return back();
    }

    public function destroy(ShoppingList $shoppingList): RedirectResponse
    {
        $shoppingList->delete();

        return back();
    }

    public function addFromRecipe(Recipe $recipe): RedirectResponse
    {
        $household = auth()->user()->activeHousehold();

        $recipe->load('recipeProducts.product');

        foreach ($recipe->recipeProducts as $recipeProduct) {
            $formatted = MeasurmentConversionService::fromBaseAmount($recipeProduct->amount, $recipeProduct->product);

            $household->shoppingList()->create([
                'household_id' => $household->id,
                'name' => $recipeProduct->product->name . ' ' . $formatted['amount'] . $formatted['unit'],
            ]);
        }

        return back()->with('success', 'Receptes produkti ir pievienoti iepirkšanās sarakstam');
    }

    public function addFromRecipeHousehold(Recipe $recipe): RedirectResponse
    {
        $household = auth()->user()->activeHousehold();

        $recipe->load('recipeProducts.product');
        $household->load('householdProducts');

        foreach ($recipe->recipeProducts as $recipeProduct) {
            $product = $recipeProduct->product;
            $requiredAmount = $recipeProduct->amount;

            $householdProduct = $household->householdProducts->firstWhere('product_id', $product->id);
            $ownedAmount = $householdProduct?->amount ?? 0;

            if ($ownedAmount >= $requiredAmount) {
                continue;
            }

            $missingAmount = $requiredAmount - $ownedAmount;

            $formatted = MeasurmentConversionService::fromBaseAmount($missingAmount, $product);

            $household->shoppingList()->create([
                'household_id' => $household->id,
                'name' => $product->name . ' ' . $formatted['amount'] . $formatted['unit'],
            ]);
        }

        return back()->with('success', 'Trūkstošie produkti ir pievienoti iepirkšanās sarakstam');
    }
}
