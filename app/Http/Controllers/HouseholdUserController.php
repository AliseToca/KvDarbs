<?php

namespace App\Http\Controllers;

use App\Enums\HouseholdUser\Role;
use App\Models\Household;
use App\Models\User;
use App\Services\HouseholdUrlService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HouseholdUserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private HouseholdUrlService $householdUrlService) {}

    public function update(Request $request, Household $household): RedirectResponse
    {
        $this->authorize('manageMember', $household);

        $validated = $request->validate([
            'users'        => ['required', 'array'],
            'users.*.id'   => ['required', 'exists:users,id'],
            'users.*.role' => ['required', 'string', 'in:owner,member'],
        ]);

        foreach ($validated['users'] as $userData) {
            $household->users()->updateExistingPivot($userData['id'], [
                'role' => $userData['role'],
            ]);
        }

        return back()->with('success', 'Lomas veiksmīgi atjaunotas');
    }

    public function destroy(Household $household, User $user): RedirectResponse
    {
        $this->authorize('manageMember', $household);

        $household->users()->detach($user->id);

        return back()->with('success', 'Lietotājs veiksmīgi noņemts');
    }

    public function leave(Request $request, Household $household): RedirectResponse
    {
        $this->authorize('leave', $household);

        $user = $request->user();

        $role = $household->users()
            ->where('user_id', $user->id)
            ->first()
            ->pivot->role;

        if ($role === Role::Owner) {
            $nextUser = $household->users()
                ->where('user_id', '!=', $user->id)
                ->first();

            if ($nextUser) {
                $household->users()->updateExistingPivot($nextUser->id, ['role' => Role::Owner]);
            }
        }

        $user->households()->detach($household->id);

        if ($household->users()->count() === 0) {
            $household->delete();
        }

        if ($user->households()->count() === 0) {
            $newHousehold = Household::create([
                'name' => $user->name . " Mājsaimniecība",
            ]);

            $user->households()->attach($newHousehold->id, ['role' => Role::Owner]);
        }


        return redirect($this->householdUrlService->indexUrl())
            ->with('success', 'Veiksmīgi atvienojies no mājsaimniecības');
    }
}
