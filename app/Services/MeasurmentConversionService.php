<?php

namespace App\Services;
use App\Models\Product;
use App\Models\Unit;

class MeasurmentConversionService
{
    private const VOLUME_EXCLUDED_UNITS = ['ēdmk.', 'tējk.'];

    public static function toBaseAmount(float $amount, Unit $unit, Product $product): float
    {
        if ($unit->measurement_type_id !== $product->measurement_type_id)
        {
            throw new \InvalidArgumentException(
                'Unit is not compatible with this product.'
            );
        }

        // Pārveido produkta daudzumu uz tā bāzes daudzumu
        return $amount * $unit->conversion_factor;
    }

    public static function fromBaseAmount(float $baseAmount, Product $product): array {
        // Iegūst visas iespējamās produkta mērvienības
        $product_units = $product->measurementType
            ->units
            ->sortByDesc('conversion_factor');

        // Izfiltrē ēdmk. un tējk. mērvienības pēc to konversijas faktoriem
        $excluded_factors = $product_units
            ->whereIn('name', self::VOLUME_EXCLUDED_UNITS)
            ->pluck('conversion_factor');

        if ($excluded_factors->isNotEmpty()) {
            $product_units = $product_units->filter(
                fn ($unit) => !$excluded_factors->contains($unit->conversion_factor)
            );
        }

        // Atrod atbilstošāko mērvienību atkarībā no produkta daudzuma
        $unit = $product_units->first(fn ($unit) => $baseAmount >= $unit->conversion_factor) ?: $product_units->last();

        // Pārveido bāzes daudzumu uz daudzumu mērvienībā
        $amount = round($baseAmount / $unit->conversion_factor, 2);

        return [
            'amount' => $amount,
            'unit' => $unit->name,
            'unit_id' => $unit->id,
        ];
    }
}
