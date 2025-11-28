<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Helpers\ReloadsPageRoutes;
use Tests\Helpers\WithPageHelper;

abstract class TestCase extends BaseTestCase
{
    use WithPageHelper;
    use ReloadsPageRoutes;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->pageTree()->createLanguagePage();
        $this->reloadPageRoutes();
    }

    public function tearDown(): void
    {
        $this->pageTree()->deleteAllPages();

        parent::tearDown();
    }
}
