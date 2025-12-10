<?php

namespace App\Models;

use App\Filament\Templates\HomePageTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Waavi\Translation\Models\Language;
use App\Models\Page;
use App\Models\MenuItem;

class Menu extends Model
{
    protected $fillable = [
        'language_id',
        'name',
        'type',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }
}
