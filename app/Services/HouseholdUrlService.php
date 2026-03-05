<?php

namespace App\Services;

use App\Models\User;
use App\Filament\Templates\HouseholdTemplate;
use CubeAgency\FilamentPageManager\Models\Page;

class HouseholdUrlService
{
    public function __construct(protected PagesService $pagesService) {}

    public function indexUrl(): string
    {
        $languagePage = $this->pagesService->getLanguagePage();

        $page = Page::query()
            ->where('template', HouseholdTemplate::class)
            ->where('parent_id', $languagePage->id)
            ->firstOrFail();

        return $page->getUrl('index');
    }

    public function showUrl(User $user): string
    {
        $languagePage = $this->pagesService->getLanguagePage();

        $page = Page::query()
            ->where('template', HouseholdTemplate::class)
            ->where('parent_id', $languagePage->id)
            ->firstOrFail();

        return $page->getUrl('show', [
            'user' => $user->username,
        ]);
    }
}
