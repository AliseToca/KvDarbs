<?php

namespace App\Models\Cookies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class CookieGroup extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $table = 'cookie_groups';

    protected $fillable = [
        'name',
        'title',
        'description',
        'position',
        'active',
        'enabled_by_default',
        'is_mandatory'
    ];

    protected array $translatable = [
        'title',
        'description'
    ];

    public function cookies(): HasMany
    {
        return $this->hasMany(Cookie::class);
    }
}
