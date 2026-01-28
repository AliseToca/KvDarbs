<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeasurementType extends Model
{
    protected $fillable =
    [
        'name',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }
}
