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
    ];

    public function households(){
        return $this->belongsToMany(Household::class);
    }

    public function shoppingLists(){
        return $this->belongsToMany(ShoppingList::class);
    }

    public function recipes(){
        return $this->belongsToMany(Recipe::class);
    }

    public function productCategories(){
        return $this->belongsTo(ProductCategory::class);
    }
}
