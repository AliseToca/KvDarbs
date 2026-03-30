<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\HouseholdProduct;
use App\Models\Unit;
use App\Models\Product;
use App\Services\MeasurmentConversionService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class HouseholdProductController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validē lietotāja ievadītos datus
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'amount' => 'required|numeric',
            'unit_id' => 'required|exists:units,id',
            'expiration_date' => 'nullable|date',
        ]);

        // Iegūstam nepieciešamos modeļus
        $household = auth()->user()->activeHousehold();
        $unit = Unit::findOrFail($validated['unit_id']);
        $product = Product::findOrFail($validated['product_id']);

        // Pārveidojam lietotāja ievadīto daudzumu uz tā bāzes vienību
        $amountInBaseUnit = MeasurmentConversionService::toBaseAmount($validated['amount'], $unit, $product);

        // Iegūstan mājsaimniecībā esošu produktu, ja tas ir
        $existingProduct = $household->householdProducts()
            ->where('product_id', $product->id)
            ->first();

        // Ja mājsaimniecībā ir produkts, tam pieskaita lietotāja ievadīto daudzumu
        if ($existingProduct && $existingProduct->expiration_date == $validated['expiration_date'])
        {
            $existingProduct->increment('amount', $amountInBaseUnit);
        } else // citādāk tas tiek izveidots
        {
            $household->householdProducts()->create([
                'product_id' => $product->id,
                'amount' => $amountInBaseUnit,
                'expiration_date' => $validated['expiration_date'],
            ]);
        }

        return back()->with('success', 'Veiksmīgi pievienots produkts');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HouseholdProduct $householdProduct): RedirectResponse
    {
        // Validē lietotāja ievadītos datus
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'unit_id' => 'required|exists:units,id',
            'expiration_date' => 'nullable|date',
        ]);

        // Iegūstam nepieciešamos modeļus
        $unit = Unit::findOrFail($validated['unit_id']);
        $product = $householdProduct->product;

        // Pārveidojam lietotāja ievadīto daudzumu uz bāzes daudzumu
        $amountInBaseUnit = MeasurmentConversionService::toBaseAmount($validated['amount'], $unit, $product);

        // Atjaunina mājsaimniecības produkta datus
        $householdProduct->update([
            'amount' => $amountInBaseUnit,
            'expiration_date' => $validated['expiration_date'],
        ]);

        return back()->with('success', 'Veiksmīgi atjaunota produkta informācija');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HouseholdProduct $householdProduct): RedirectResponse
    {
        $householdProduct->delete();


        return back()->with('success', 'Produkts tika dzēsts no mājsaimniecības');
    }
}
