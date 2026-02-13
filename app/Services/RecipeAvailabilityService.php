<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\User;

class RecipeAvailabilityService
{
    public static function calculate(Recipe $recipe, User $user): array
    {
        $recipe->load('recipeProducts.product');
        $user->load('household.householdProducts.product');

        $householdProducts = $user->household->householdProducts->keyBy('product_id');

        if (!$user->household) {
            return [
                'available_products_count' => 0,
                'missing_products_count' => $recipe->recipeProducts->count(),
                'total_products_count' => $recipe->recipeProducts->count(),
                'compatibility' => 0,
            ];
        }

        $available = 0;
        $missing = 0;

        foreach($recipe->recipeProducts as $recipeProduct) {
            $requiredAmount = $recipeProduct->amount;

            $householdProduct = $householdProducts->get($recipeProduct->product_id);

            if(!$householdProduct) {
                $missing++;
                continue;
            }if($householdProduct->amount >= $requiredAmount) {
                $available++;
            }else{
                $missing++;
            }
        }

        return [
            'available_products_count' => $available,
            'missing_products_count' => $missing,
            'total_products_count' => $available + $missing,
            'compatibility' => $available / ($available + $missing) * 100,
        ];
    }
}
