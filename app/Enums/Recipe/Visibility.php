<?php

namespace App\Enums\Recipe;

enum Visibility: string
{
    case Public = 'public';
    case Household = 'household';
    case Private = 'private';

    public function label(): string
    {
        return match($this) {
            self::Public    => __('recipe.visibility.public'),
            self::Private   => __('recipe.visibility.private'),
            self::Household => __('recipe.visibility.household'),
        };
    }
}
