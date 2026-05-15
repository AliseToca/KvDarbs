<?php

namespace App\Http\Controllers;

use App\Models\HouseholdProduct;
use App\Models\Product;
use App\Models\Unit;
use App\Services\MeasurmentConversionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HouseholdProductController extends Controller
{
    /**
     * Adds a product to the authenticated user's active household.
     *
     * If the same product already exists in the household with a matching
     * expiration date, the stored amount is incremented rather than creating
     * a duplicate entry. Different expiration dates are treated as separate
     * stock items (e.g. a fresh batch alongside an older one).
     *
     * All amounts are converted to the product's base unit before storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id'      => 'required|exists:products,id',
            'amount'          => 'required|numeric',
            'unit_id'         => 'required|exists:units,id',
            'expiration_date' => 'nullable|date',
        ]);

        $household = auth()->user()->activeHousehold();
        $unit      = Unit::findOrFail($validated['unit_id']);
        $product   = Product::findOrFail($validated['product_id']);

        $amountInBaseUnit = MeasurmentConversionService::toBaseAmount(
            $validated['amount'],
            $unit,
            $product
        );

        $existingProduct = $household->householdProducts()
            ->where('product_id', $product->id)
            ->first();

        $sameExpirationDate = $existingProduct
            && $existingProduct->expiration_date == $validated['expiration_date'];

        if ($sameExpirationDate) {
            // Merge into the existing stock entry rather than creating a duplicate.
            $existingProduct->increment('amount', $amountInBaseUnit);
        } else {
            $household->householdProducts()->create([
                'product_id'      => $product->id,
                'amount'          => $amountInBaseUnit,
                'expiration_date' => $validated['expiration_date'],
            ]);
        }

        return back()->with('success', 'Produkts veiksmīgi pievienots');
    }

    /**
     * Replaces the amount and expiration date of an existing household product.
     *
     * The incoming amount is converted from the user-selected unit to the
     * product's base unit before being stored.
     */
    public function update(Request $request, HouseholdProduct $householdProduct): RedirectResponse
    {
        $validated = $request->validate([
            'amount'          => 'required|numeric',
            'unit_id'         => 'required|exists:units,id',
            'expiration_date' => 'nullable|date',
        ]);

        $unit    = Unit::findOrFail($validated['unit_id']);
        $product = $householdProduct->product;

        $amountInBaseUnit = MeasurmentConversionService::toBaseAmount(
            $validated['amount'],
            $unit,
            $product
        );

        $householdProduct->update([
            'amount'          => $amountInBaseUnit,
            'expiration_date' => $validated['expiration_date'],
        ]);

        return back()->with('success', 'Produkta informācija viekmsīgi atjaunota');
    }

    /**
     * Removes a product entry from the household.
     */
    public function destroy(HouseholdProduct $householdProduct): RedirectResponse
    {
        $householdProduct->delete();

        return back()->with('success', 'Produkts veiksmīgi dzēsts no mājsaimniecības');
    }
}
