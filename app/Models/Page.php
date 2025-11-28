<?php

namespace App\Models;

use CubeAgency\FilamentPageManager\Models\Page as FilamentPage;

class Page extends FilamentPage
{
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'template',
        'content',
        'meta',
        'related_pages',
        'activate_at',
        'expire_at',
    ];

    protected $casts = [
        'content' => 'array',
        'meta' => 'array',
        'related_pages' => 'array',
    ];

    public function getMetaImageAttribute()
    {
        return $this->meta['image'] ?? null;
    }
}
