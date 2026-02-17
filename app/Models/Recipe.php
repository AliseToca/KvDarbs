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
use App\Enums\Recipe\Visibility;
use Illuminate\Database\Eloquent\Builder;

class Recipe extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'visibility',
        'image_src',
        'content',
        'prep_time',
        'cook_time',
        'servings',
        'instructions',
        'product_id',
        'user_id',
    ];

    // Datu pārveidošana uz tipu
    protected $casts = [
        'image_src' => 'string',
        'ingredients' => 'array',
        'instructions' => 'array',
        'visibility' => Visibility::class,
    ];

    // Vērtību piekļuves lauki
    protected $appends = [
        'total_time',
        'average_rating',
        'reviews_count',
    ];

    //--- Receptes relācijas ---
    public function user(): BelongsTo
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

    //--- Vērtibu piekļuves metodes ---
    // Kopējā receptes laika aprēķins
    public function getTotalTimeAttribute(): int
    {
        return ($this->prep_time ?? 0) + ($this->cook_time ?? 0);
    }

    // Receptes vidējā novērtējuma iegūšana
    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->reviews()->avg('rating');

        return $avg !== null ? round($avg, 1) : null;
    }

    // Receptes atsauksmju skaita iegūšana
    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $q) use ($user){
            // Publiski ieraksti - redzami visiem
            $q->where('visibility', Visibility::Public)
                // Privāti ieraksti - redzami tikai pašam lietotājam
                ->orWhere(function (Builder $q) use ($user) {
                    $q->where('visibility', Visibility::Private)
                        ->where('user_id', $user->id);
                })
                // Mājsaimniecības ieraksti - redzami tikai lietotājiem no tās pašas mājsaimniecības
                ->orWhere(function(Builder $q) use ($user) {
                    $q->where('visibility', Visibility::Household)
                        ->whereHas('user', fn($q) =>
                            $q->where('household_id', $user->household_id)
                        );
                });
        });
    }
}
