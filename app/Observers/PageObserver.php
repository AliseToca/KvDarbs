<?php

namespace App\Observers;

use App\Services\PagesService;
use CubeAgency\FilamentPageManager\Models\Page;

class PageObserver
{
    public function __construct(protected PagesService $pagesService)
    {
    }

    public function saving(Page $model): void
    {
        $this->pagesService->clearCache();
    }

    public function deleted(Page $model): void
    {
        $this->pagesService->clearCache();
    }
}
