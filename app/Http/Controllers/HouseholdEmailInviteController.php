<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Household;
use App\Models\HouseholdInvitation;
use App\Mail\HouseholdInviteMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Services\HouseholdUrlService;

class HouseholdEmailInviteController extends Controller
{
    public function __construct(protected HouseholdUrlService $householdUrlService) {}

    public function send(Request $request, Household $household)
    {
        $isOwner = $household->users()
            ->where('user_id', auth()->id())
            ->where('household_user.role', 'owner')
            ->exists();

        $request->validate(['email' => 'required|email']);

        $email = $request->email;

        $alreadyMember = $household->users()
            ->where('email', $email)
            ->exists();

        if ($alreadyMember) {
            return back()->withErrors(['email' => 'This person is already a member.']);
        }

        $invitation = HouseholdInvitation::updateOrCreate(
            ['household_id' => $household->id, 'email' => $email],
            [
                'invited_by' => auth()->id(),
                'token' => Str::random(64),
                'expires_at' => now()->addDays(7),
                'accepted_at' => null,
            ]
        );
        $invitation->load(['household', 'inviter']);

        Mail::to($email)->send(new HouseholdInviteMail($invitation));

        return back()->with('success', "Invite sent to {$email}!");
    }

    public function show(string $token)
    {
        $invitation = HouseholdInvitation::with(['household', 'inviter'])
            ->where('token', $token)
            ->firstOrFail();

        abort_if(!$invitation->isValid(), 410, 'This invite has expired or already been used.');

        if (!auth()->check()) {
            session(['url.intended' => route('households.invite.email.show', $token)]);
        }

        return Inertia::render('Household/AcceptInvite', [
            'invitation' => [
                'token'     => $token,
                'household' => $invitation->household->only('name'),
                'inviter'   => $invitation->inviter->only('name'),
                'email'     => $invitation->email,
            ],
            'isLoggedIn'   => auth()->check(),
            'emailMatches' => auth()->check() && auth()->user()->email === $invitation->email,
        ]);
    }

    public function accept(string $token)
    {
        $invitation = HouseholdInvitation::with('household')
            ->where('token', $token)
            ->firstOrFail();

        abort_if(!$invitation->isValid(), 410, 'This invite has expired or already been used.');

        $user = auth()->user();
        $household = $invitation->household;

        $alreadyMember = $household->users()->where('user_id', $user->id)->exists();

        if (!$alreadyMember) {
            $household->users()->attach($user->id, ['role' => 'member']);
        }

        $invitation->update(['accepted_at' => now()]);

        return redirect(
            $this->householdUrlService->showUrl($user)
        )->with('success', "Welcome to {$household->name}!");
    }

    public function cancel(Household $household, HouseholdInvitation $invitation)
    {
        abort_if(auth()->id() !== $household->owner_id, 403);
        $invitation->delete();
        return back()->with('success', 'Invitation cancelled.');
    }
}
