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
        $shoppingList = $household->shoppingList;

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
}
