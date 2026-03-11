<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\HouseholdProduct;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Household extends Model
{
    protected $fillable = [
        'name',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function householdProducts(): HasMany
    {
        return $this->hasMany(HouseholdProduct::class);
    }

    public function shoppingList(): HasOne
    {
        return $this->hasOne(ShoppingList::class);
    }

    public function getHouseholdUsersCountAttribute(): int
    {
        return $this->users()->count();
    }
}
