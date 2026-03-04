<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;

class HouseholdInvitation extends Model
{
    protected $fillable = [
        'household_id',
        'invited_by',
        'email',
        'token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isValid(): bool
    {
        return !$this->accepted_at && $this->expires_at->isFuture();
    }

    public function getJoinUrlAttribute(): string
    {
        return url("/households/join-email/{$this->token}");
    }
}
