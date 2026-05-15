<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\User;

class RecipeAvailabilityService
{
    /**
     * Calculates how many of a recipe's required products are available
     * in the user's active household, and returns a compatibility percentage.
     *
     * Returns all-zero / 0% compatibility when the user has no active household.
     */
    public static function calculate(Recipe $recipe, User $user): array
    {
        $recipe->load('recipeProducts.product');

        $total = $recipe->recipeProducts->count();

        $household = $user->activeHousehold();

        if (!$household) {
            return self::unavailableResult($total);
        }

        $household->load('householdProducts.product');

        // Key by product_id for O(1) lookups inside the loop.
        $householdProducts = $household->householdProducts->keyBy('product_id');

        $available = 0;

        foreach ($recipe->recipeProducts as $recipeProduct) {
            $householdProduct = $householdProducts->get($recipeProduct->product_id);

            // A product counts as available only when the household holds
            // at least the required amount (exact match is acceptable).
            $isAvailable = $householdProduct
                && $householdProduct->amount >= $recipeProduct->amount;

            if ($isAvailable) {
                $available++;
            }
        }

        $missing = $total - $available;

        return [
            'available_products_count' => $available,
            'missing_products_count'   => $missing,
            'total_products_count'     => $total,
            'compatibility'            => $total > 0 ? ($available / $total) * 100 : 0,
        ];
    }

    /**
     * Returns a zeroed-out result for when availability cannot be determined
     * (e.g. the user has no active household).
     */
    private static function unavailableResult(int $total): array
    {
        return [
            'available_products_count' => 0,
            'missing_products_count'   => $total,
            'total_products_count'     => $total,
            'compatibility'            => 0,
        ];
    }
}
