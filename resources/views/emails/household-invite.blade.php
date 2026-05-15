<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; padding: 20px; display: flex; flex-direction: column; align-items: center; text-align: center;">
    <p><strong>{{ $invitation->inviter->name }}</strong> aicina tevi pievienoties mājsaimniecībai <strong>"{{ $invitation->household->name }}"</strong>.</p>

    <a href="{{ $invitation->join_url }}" style="display:inline-block;background:#dd3333;color:white;padding:12px 24px;text-decoration:none;border-radius:6px;margin:16px 0;">
        Apstiprināt uzaicinājumu
    </a>

    <p>Uzaicinājuma termiņš ir <strong>7 dienas</strong>.</p>

    <p>{{ config('app.name') }}</p>
</body>
</html>
