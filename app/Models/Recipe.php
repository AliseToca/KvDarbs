<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Review;
use App\Models\Folder;
use App\Models\Product;
use App\Models\RecipeCategory;
use App\Models\RecipeType;


class Recipe extends Model
{
    protected $fillable = [
        'name',
        'image',
        'content',
        'time_needed_minutes',
        'servings',
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function reviews(){
        return $this->hasMany(Review::class);
    }

    public function folders(){
        return $this->belongsToMany(Folder::class);
    }

    public function products(){
        return $this->belongsToMany(Product::class);
    }

    public function recipeCategories(){
        return $this->belongsToMany(RecipeCategory::class);
    }

    public function recipeTypes(){
        return $this->belongsToMany(RecipeType::class);
    }
}
