<?php

namespace App\Policies;

use App\Models\Household;
use App\Models\User;

class HouseholdPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Household $household): bool
    {
        return $user->households()
            ->where('households.id', $household->id)
            ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Household $household): bool
    {
        return false;
    }

    public function leave(User $user, Household $household): bool
    {
        $isMember = $user->households()
            ->where('households.id', $household->id)
            ->exists();

        if (!$isMember) {
            return false;
        }

        $role = $user->households()
            ->where('households.id', $household->id)
            ->first()
            ->pivot->role;

        if ($role === 'owner') {
            return $household->users()->count() > 1;
        }

        return true;
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Household $household): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Household $household): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Household $household): bool
    {
        return false;
    }
}
