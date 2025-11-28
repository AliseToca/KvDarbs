<?php

namespace Tests\Helpers;

use CubeAgency\FilamentPageManager\Services\PageRoutes;

trait ReloadsPageRoutes
{
    public function reloadPageRoutes(): void
    {
        PageRoutes::register();
        app('router')->getRoutes()->refreshNameLookups();
        app('router')->getRoutes()->refreshActionLookups();
    }
}
