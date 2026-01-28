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
        'id',
        'name',
        'product_category_id',
        'measurement_type_id',
    ];

    public function households(): BelongsToMany
    {
        return $this->belongsToMany(Household::class);
    }

    public function shoppingLists(): BelongsToMany
    {
        return $this->belongsToMany(ShoppingList::class);
    }

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'product_recipe_unit')
            ->withPivot(['amount', 'unit_id'])
            ->withTimestamps();
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function measurementType(): BelongsTo
    {
        return $this->belongsTo(MeasurementType::class);
    }
}
