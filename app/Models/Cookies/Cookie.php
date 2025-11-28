<?php

namespace App\Models\Cookies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Cookie extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $table = 'cookies';

    protected $fillable = [
        'cookie_group_id',
        'title',
        'provider',
        'purpose',
        'expiration',
        'type'
    ];

    protected array $translatable = [
        'purpose',
        'expiration'
    ];

    public function cookieGroup():BelongsTo
    {
        return $this->belongsTo(CookieGroup::class);
    }
}
