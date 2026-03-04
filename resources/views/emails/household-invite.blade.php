@component('mail::message')
    **{{ $invitation->inviter->name }}** has invited you to join their household **{{ $invitation->household->name }}** on {{ config('app.name') }}.

    @component('mail::button', ['url' => $invitation->join_url])
        Accept Invitation
    @endcomponent

    This invite expires in **7 days**. If you didn't expect this, you can safely ignore it.

    Thanks,
    {{ config('app.name') }}
@endcomponent
