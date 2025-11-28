<?php

namespace Tests\Helpers;

use CubeAgency\FilamentPageManager\Models\Page;

trait WithPage
{
    public function makePage($attributes = [], $count = 1)
    {
        return Page::factory()->count($count)->make($attributes);
    }

    public function createPage($attributes = [], $count = 1)
    {
        return Page::factory()->count($count)->create($attributes);
    }
}
