<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeProduct extends Model
{
    protected $table = 'recipe_products';

    protected $fillable = [
        'recipe_id',
        'product_id',
        'amount',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
