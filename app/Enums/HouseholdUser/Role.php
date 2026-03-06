<?php

namespace App\Enums\HouseholdUser;

enum Role: string
{
    case Owner = 'owner';
    case Member = 'member';

    public function label(): string
    {
        return match($this) {
            self::Owner    => __('household.role.owner'),
            self::Member   => __('household.role.member'),
        };
    }
}
