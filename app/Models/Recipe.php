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
use App\Services\PagesService;

class Recipe extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'image_src',
        'content',
        'prep_time',
        'cook_time',
        'servings',
        'instructions',
        'product_id',
    ];

    protected $casts = [
        'image_src' => 'string',
        'ingredients' => 'array',
        'instructions' => 'array',
    ];

    protected $appends = [
        'total_time'
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function folders(): BelongsToMany
    {
        return $this->belongsToMany(Folder::class);
    }

    public function recipeProducts(): HasMany
    {
        return $this->hasMany(RecipeProduct::class);
    }

    public function recipeCategories(): BelongsToMany
    {
        return $this->belongsToMany(RecipeCategory::class);
    }

    public function recipeTypes(): BelongsToMany
    {
        return $this->belongsToMany(RecipeType::class);
    }

    public function getUrlAttribute(): string
    {
        $pages = app(PagesService::class);

        $language = $pages->getLanguagePage();
        $page = $pages->getCurrentPage();

        return url(sprintf(
            '/%s/%s/%s',
            $language->slug,
            $page->slug,
            $this->slug
        ));
    }

    public function getTotalTimeAttribute(): int
    {
        return ($this->prep_time ?? 0) + ($this->cook_time ?? 0);
    }
}
