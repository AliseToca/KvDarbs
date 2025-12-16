<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Household;
use App\Models\ShoppingList;
use App\Models\Recipe;
use App\Models\ProductCategory;

class Product extends Model
{
    protected $fillable = [
        'name',
        'product_category_id',
    ];

    public function households(){
        return $this->belongsToMany(Household::class);
    }

    public function shoppingLists(){
        return $this->belongsToMany(ShoppingList::class);
    }

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'product_recipe_unit')
            ->withPivot(['amount', 'unit_id'])
            ->withTimestamps();
    }

    public function productCategory(){
        return $this->belongsTo(ProductCategory::class);
    }
}
