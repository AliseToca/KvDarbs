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

    /**
     * Send an email invitation to join a household.
     * Only the household owner can send invitations.
     */
    public function send(Request $request, Household $household)
    {
        // Verify the authenticated user is the household owner
        $isOwner = $household->users()
            ->where('user_id', auth()->id())
            ->where('household_user.role', 'owner')
            ->exists();

        abort_if(!$isOwner, 403);

        $request->validate(['email' => 'required|email']);

        $email = $request->email;

        // Prevent inviting someone who is already a member
        $alreadyMember = $household->users()
            ->where('email', $email)
            ->exists();

        if ($alreadyMember) {
            return back()->withErrors(['email' => 'Lietotājs jau ir mājsaimniecībā']);
        }

        // Create a new invitation or refresh an existing one for this email,
        // resetting the token and expiry in case it was previously sent
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

        return back()->with('success', "Uzaicinājums nosūtīts {$email}!");
    }

    /**
     * Display the invitation acceptance page for a given token.
     * Passes login/email-match state to the frontend to render the correct UI.
     */
    public function show(string $token)
    {
        $invitation = HouseholdInvitation::with(['household', 'inviter'])
            ->where('token', $token)
            ->firstOrFail();

        // Reject expired or already-accepted invitations
        abort_if(!$invitation->isValid(), 410, 'Šis uzaicinājums ir beidzies vai jau izmantots');

        // Store the intended URL so unauthenticated users are redirected back here after login
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
            // True only if the logged-in user's email matches the invitation recipient
            'emailMatches' => auth()->check() && auth()->user()->email === $invitation->email,
        ]);
    }

    /**
     * Accept the invitation and attach the authenticated user to the household.
     */
    public function accept(string $token)
    {
        $invitation = HouseholdInvitation::with('household')
            ->where('token', $token)
            ->firstOrFail();

        abort_if(!$invitation->isValid(), 410, 'Šis uzaicinājums ir beidzies vai jau izmantots');

        $user = auth()->user();

        // Enforces that the logged-in user is actually the intended recipient
        abort_if($user->email !== $invitation->email, 403, 'Šis uzaicinājums nav paredzēts jums');

        $user = auth()->user();
        $household = $invitation->household;

        // Only attach the user if they aren't already a member (e.g. edge case / double submit)
        $alreadyMember = $household->users()->where('user_id', $user->id)->exists();

        if (!$alreadyMember) {
            $household->users()->attach($user->id, ['role' => 'member']);
        }

        // Mark the invitation as accepted so it can't be reused
        $invitation->update(['accepted_at' => now()]);

        // Redirect to the household page with a success message
        return redirect(
            $this->householdUrlService->showUrl($user)
        )->with('success', "Sveicināti '{$household->name}'!");
    }

    /**
     * Cancel (delete) a pending invitation.
     * Only the household owner can cancel invitations.
     */
    public function cancel(Household $household, HouseholdInvitation $invitation)
    {
        abort_if(auth()->id() !== $household->owner_id, 403);
        $invitation->delete();
        return back()->with('success', 'Ielūgums atcelts');
    }
}
