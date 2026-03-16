<?php

namespace App\Actions\Fortify;

use App\Models\Household;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Models\HouseholdInvitation;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:25', Rule::unique(User::class)],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'username' => $input['username'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $token = $input['invite_token'] ?? null;
        $invite = null;

        if ($token) {
            $invite = HouseholdInvitation::with('household')
                ->where('token', $token)
                ->where('email', $user->email)
                ->whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->first();
        }

        if ($invite) {
            $invite->household->users()->attach($user->id, ['role' => 'member']);
            $invite->update(['accepted_at' => now()]);
        } else {
            $household = Household::create(['name' => trans('household.your')]);
            $household->users()->attach($user->id, ['role' => 'owner']);
        }

        return $user;
    }
}
