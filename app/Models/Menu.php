<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Waavi\Translation\Models\Language;
use App\Models\MenuItem;


class Menu extends Model
{
    protected $fillable = [
        'languange_id',
        'name',
        'type',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function items()
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }
}
