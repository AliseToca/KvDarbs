<?php

namespace App\Services;

use App\Filament\Templates\LanguageTemplate;
use App\Filament\Templates\RecipeTemplate;
use App\Models\Page;
use App\Repositories\LanguageRepository;
use Illuminate\Cache\TaggedCache;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PagesService
{
    protected ?Model $languagePage = null;

    protected const int CACHE_TTL = 300;
    protected const string CACHE_TAG = 'pages';

    protected const array PAGE_COLUMNS = [
        'id',
        'parent_id',
        'name',
        'slug',
        'template',
        'related_pages',
        'content',
        'meta',
    ];

    public function __construct(protected LanguageRepository $languageRepository)
    {
    }

    public function getCurrentPage(): Page|Model|null
    {
        if (!$this->getRouter()->getCurrentRoute()) {
            return null;
        }

        return $this->getPageFromRoute($this->getRouter()->getCurrentRoute());
    }

    public function getLanguagePage(string $locale = null): Page|Model|null
    {
        $locale = $locale ?? app()->getLocale();
        $cacheKey = 'language-' . $locale;

        $callback = function () use ($locale) {
            return Page::query()
                ->select(self::PAGE_COLUMNS)
                ->active()
                ->whereNull('parent_id')
                ->where('template', LanguageTemplate::class)
                ->where('content->locale', $locale)
                ->first();
        };

        return $this->getCacheStore()->remember($cacheKey, self::CACHE_TTL, $callback);
    }

    public function getRecipeIndexPage(): Page
    {
        $languagePage = $this->getLanguagePage();

        return Page::query()
            ->select(['id', 'name', 'content->blocks as blocks'])
            ->where('template', RecipeTemplate::class)
            ->where('parent_id', $languagePage->id)
            ->firstOrFail();
    }


    public function getPageByTemplate(string $template): Page|Model|null
    {
        $languagePage = $this->getLanguagePage();
        if (!$languagePage) {
            return null;
        }

        return $this->getCacheStore()->remember(
            $languagePage->getKey() . '-' . $template,
            self::CACHE_TTL,
            function () use ($languagePage, $template) {
                return $languagePage
                    ?->descendants()
                    ->active()
                    ->where('template', $template)
                    ->first();
            }
        );
    }

    public function getPagesByTemplate(string $template): Collection
    {
        return $this->getLanguagePage()
            ?->descendants()
            ->active()
            ->where('template', $template)
            ->get();
    }

    public function getPageById(int $id): Page|Model|null
    {
        return $this->getCacheStore()
            ->remember(
                $id,
                self::CACHE_TTL,
                function () use ($id) {
                    return Page::query()->active()->find($id);
                }
            );
    }

    public function getRelatedPage(string $locale): Model|Page|null
    {
        $currentPage = $this->getCurrentPage();
        if (!$currentPage) {
            return null;
        }

        $relatedPages = collect($currentPage->related_pages);
        if ($relatedPages->isEmpty()) {
            return $this->getLanguagePage($locale);
        }

        $relatedPages = $relatedPages->mapWithKeys(
            fn($relatedPage) => [$relatedPage['locale'] => $relatedPage['page_id']]
        );
        $relatedPage = $relatedPages[$locale] ?? null;
        if (!$relatedPage) {
            return $this->getLanguagePage($locale);
        }

        return $this->getPageById($relatedPage);
    }

    public function clearCache(): bool
    {
        return $this->getCacheStore()->flush();
    }

    protected function getPageFromRoute(Route $route): Page|Model|null
    {
        $currentRouteName = $route->getName();

        if (!preg_match('#^page\.(?P<id>.*?)\.#', $currentRouteName, $matches)) {
            return null;
        }

        $id = $matches['id'];

        return $this->getPageById($id);
    }

    protected function getRouter(): Router
    {
        return app('router');
    }

    protected function getCacheStore(): Repository|TaggedCache
    {
        return Cache::supportsTags()
            ? Cache::tags(self::CACHE_TAG)
            : Cache::store();
    }
}
