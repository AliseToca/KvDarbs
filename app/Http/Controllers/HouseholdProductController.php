<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\HouseholdProduct;
use App\Models\Unit;
use App\Models\Product;
use Illuminate\Http\Request;

class HouseholdProductController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'household_id' => 'required|exists:households,id',
            'product_id' => 'required|exists:products,id',
            'amount' => 'required|numeric',
            'unit_id' => 'required|exists:units,id',
            'expiration_date' => 'nullable|date',
        ]);

        $household = Household::findOrFail($validated['household_id']);
        $unit = Unit::findOrFail($validated['unit_id']);
        $product = Product::findOrFail($validated['product_id']);

        if ($unit->measurement_type_id !== $product->measurement_type_id) {
            return back()->withErrors([
                'unit_id' => 'Selected unit is not compatible with this product.',
            ]);
        }

        $amountInBaseUnit = $validated['amount'] * $unit->conversion_factor;

        $existingProduct = $household->householdProducts()
            ->where('product_id', $product->id)
            ->first();

        if ($existingProduct) {
            $existingProduct->increment('amount', $amountInBaseUnit);
        } else {
            $household->householdProducts()->create([
                'product_id' => $product->id,
                'amount' => $amountInBaseUnit,
                'expiration_date' => $validated['expiration_date'],
            ]);
        }

        return back();
    }


    /**
     * Display the specified resource.
     */
    public function show(HouseholdProduct $householdProduct)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HouseholdProduct $householdProduct)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HouseholdProduct $householdProduct)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HouseholdProduct $householdProduct)
    {
        //
    }
}
