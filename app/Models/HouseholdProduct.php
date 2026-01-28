<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Household;
use App\Models\Product;
use App\Models\Unit;

class HouseholdProduct extends Model
{
    protected $table = 'household_products';

    protected $fillable = [
        'household_id',
        'product_id',
        'amount',
        'expiration_date'
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
