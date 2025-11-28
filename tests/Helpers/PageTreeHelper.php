<?php

namespace Tests\Helpers;

use App\Filament\Templates\LanguageTemplate;
use CubeAgency\FilamentPageManager\Models\Page;
use Illuminate\Support\Str;
use Waavi\Translation\Repositories\LanguageRepository;

class PageTreeHelper
{
    use WithPage;

    protected ?Page $languageRoot;

    public function __construct(protected LanguageRepository $languageRepository)
    {
    }

    public function createLanguagePage(string $name = 'en', string $language = 'en')
    {
        $page = $this->createPage([
            'name' => $name,
            'slug' => Str::slug($name),
            'template' => LanguageTemplate::class,
            'content' => [
                'language_id' => (string)$this->languageRepository->findByLocale($language)->id
            ],
            'activate_at' => now()->subDay(),
        ])->first();

        $this->languageRoot = $page;

        return $page;
    }

    public function getLanguageRoot(): ?Page
    {
        return $this->languageRoot;
    }

    public function addPage(string $name)
    {
        $page = $this->createPage([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);
        $this->languageRoot->children()->create($page);

        return $page;
    }

    public function addChildPage(string $name, Page $parent)
    {
        $page = $this->createPage([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);
        $parent->children()->create($page);

        return $page;
    }

    public function deleteAllPages(): void
    {
        $pages = Page::query()->whereNull('parent_id')->get();

        foreach ($pages as $page) {
            $this->deletePage($page);
        }
    }

    public function deletePage(Page $page): void
    {
        if ($page->children()) {
            $page->children()->each(function (Page $page) {
                $this->deletePage($page);
            });
        }

        $page->delete();
    }
}
