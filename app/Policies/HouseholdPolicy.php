<?php

namespace App\Policies;

use App\Enums\HouseholdUser\Role;
use App\Models\Household;
use App\Models\User;

class HouseholdPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Household $household): bool
    {
        return $user->households()
            ->where('households.id', $household->id)
            ->exists();
    }

    public function edit(User $user, Household $household): bool
    {
        return $user->households()
            ->where('households.id', $household->id)
            ->wherePivot('role', Role::Owner)
            ->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Household $household): bool
    {
        return $this->edit($user, $household);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Household $household): bool
    {
        return $this->edit($user, $household);
    }

    public function manageMember(User $user, Household $household): bool
    {
        return $this->edit($user, $household);
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

        if ($role === Role::Owner) {
            return $household->users()->count() > 1;
        }

        return true;
    }

}
