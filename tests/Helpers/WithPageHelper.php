<?php

namespace Tests\Helpers;

use Tests\Helpers\PageTreeHelper;

trait WithPageHelper
{
    protected ?PageTreeHelper $pageTree = null;

    public function pageTree()
    {
        if (!$this->pageTree) {
            $this->pageTree = app(PageTreeHelper::class);
        }

        return $this->pageTree;
    }
}
