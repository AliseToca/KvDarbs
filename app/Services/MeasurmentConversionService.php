<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Unit;

class MeasurmentConversionService
{
    /**
     * Spoon-like units (tablespoon / teaspoon) that are excluded when selecting
     * a display unit, because they are ambiguous for non-volume measurement types.
     */
    private const VOLUME_EXCLUDED_UNITS = ['ēdmk.', 'tējk.'];

    /**
     * Converts a human-entered amount (in the given unit) to the product's
     * canonical base amount for storage.
     *
     * Throws if the unit belongs to a different measurement type than the product,
     * e.g. trying to store a weight product using a volume unit.
     */
    public static function toBaseAmount(float $amount, Unit $unit, Product $product): float
    {
        if ($unit->measurement_type_id !== $product->measurement_type_id) {
            throw new \InvalidArgumentException(
                'Unit is not compatible with this product.'
            );
        }

        return $amount * $unit->conversion_factor;
    }

    /**
     * Converts a stored base amount back to the most human-readable unit
     * available for the given product.
     *
     * The selected unit is the largest one whose conversion factor still fits
     * into the base amount (e.g. 1500 g → 1.5 kg). Spoon units are excluded
     * from consideration to prevent nonsensical displays for non-liquid products.
     *
     * Returns an array with keys: 'amount', 'unit' (name), 'unit_id'.
     */
    public static function fromBaseAmount(float $baseAmount, Product $product): array
    {
        // Fetch all units for this product's measurement type, largest factor first.
        $units = $product->measurementType
            ->units
            ->sortByDesc('conversion_factor');

        $units = self::excludeSpoonUnits($units);

        // Pick the largest unit that still produces a whole-or-decimal value ≥ 1.
        // Falls back to the smallest available unit if nothing fits (e.g. tiny amounts).
        $unit = $units->first(fn($unit) => $baseAmount >= $unit->conversion_factor)
            ?: $units->last();

        $amount = round($baseAmount / $unit->conversion_factor, 2);

        return [
            'amount'  => $amount,
            'unit'    => $unit->name,
            'unit_id' => $unit->id,
        ];
    }

    /**
     * Removes spoon units (and any unit sharing their conversion factor) from
     * the candidate collection so they are never chosen as a display unit.
     *
     * Filtering by conversion_factor rather than name guards against units that
     * are stored under a slightly different label but represent the same ratio.
     */
    private static function excludeSpoonUnits($units)
    {
        $excludedFactors = $units
            ->whereIn('name', self::VOLUME_EXCLUDED_UNITS)
            ->pluck('conversion_factor');

        if ($excludedFactors->isEmpty()) {
            return $units;
        }

        return $units->filter(
            fn($unit) => !$excludedFactors->contains($unit->conversion_factor)
        );
    }
}
