<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Recipe;

class RecipeCategory extends Model
{
    protected $fillable = [
        'name',
    ];

    public function recipes(){
        return $this->belongsToMany(Recipe::class);
    }
}
