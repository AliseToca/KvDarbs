<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\HouseholdProduct;
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
            'expiration_date' => 'nullable|date'
        ]);

        $household = Household::findOrFail($validated['household_id']);

        $household->householdProducts()->create([
            'product_id' => $validated['product_id'],
            'amount' => $validated['amount'],
            'unit_id' => $validated['unit_id'],
            'expiration_date' => $validated['expiration_date'] ?? null
        ]);

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
