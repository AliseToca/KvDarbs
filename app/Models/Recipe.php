<?php

namespace App\Models;

use App\Enums\Recipe\Visibility;
use App\Models\RecipeProduct;
use App\Services\PagesService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'recipe_type_id',
    ];

    protected $casts = [
        'image_src'    => 'string',
        'ingredients'  => 'array',
        'instructions' => 'array',
        'visibility'   => Visibility::class,
    ];

    /**
     * Appended virtual attributes computed on every serialisation.
     * Keep this list short — each entry triggers an extra query if not eager-loaded.
     */
    protected $appends = [
        'total_time',
        'average_rating',
        'reviews_count',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

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

    public function recipeType(): BelongsTo
    {
        return $this->belongsTo(RecipeType::class);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Sum of prep and cook time in minutes.
     */
    public function getTotalTimeAttribute(): int
    {
        return ($this->prep_time ?? 0) + ($this->cook_time ?? 0);
    }

    /**
     * Average review rating rounded to one decimal, or null when no reviews exist.
     */
    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->reviews()->avg('rating');

        return $avg !== null ? round((float) $avg, 1) : null;
    }

    /**
     * Total number of reviews for this recipe.
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Limits the query to recipes the given user is allowed to see.
     *
     * Visibility rules:
     *  - Public    -> visible to everyone, including guests (null user).
     *  - Private   -> visible only to the recipe's author.
     *  - Household -> visible to any member of a household the author belongs to.
     */
    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        // Guests may only see public recipes.
        if ($user === null) {
            return $query->where('visibility', Visibility::Public);
        }

        return $query->where(function (Builder $q) use ($user) {
            $q->where('visibility', Visibility::Public)

                ->orWhere(function (Builder $q) use ($user) {
                    // Private: only the author themselves.
                    $q->where('visibility', Visibility::Private)
                        ->where('user_id', $user->id);
                })

                ->orWhere(function (Builder $q) use ($user) {
                    // Household: the recipe's author must share at least one
                    // household with the viewing user.
                    $q->where('visibility', Visibility::Household)
                        ->whereHas('user.households', function (Builder $q) use ($user) {
                            $q->whereIn(
                                'households.id',
                                $user->households()->pluck('households.id')
                            );
                        });
                });
        });
    }
}
