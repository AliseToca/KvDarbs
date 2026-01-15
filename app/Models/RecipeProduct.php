<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Unit;

class RecipeProduct extends Model
{
    protected $table = 'recipe_products';

    protected $fillable = [
        'recipe_id',
        'product_id',
        'unit_id',
        'amount',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
