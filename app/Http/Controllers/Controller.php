<?php

namespace App\Http\Controllers;

use App\Repositories\LanguageRepository;
use App\Services\PagesService;
use CubeAgency\FilamentPageManager\Models\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;

abstract class Controller extends \Illuminate\Routing\Controller
{
    public function __construct(protected PagesService $pagesService)
    {
    }

    protected function getAction(Request $request): mixed
    {
        return $request->route() instanceof Route ?
            $request->route()->getAction() :
            abort(Response::HTTP_NOT_FOUND);
    }

    protected function loadPage(Request $request): Model
    {
        $action = $this->getAction($request);

        return Page::query()->findOrFail($action['pageId']);
    }

    protected function overrideLanguageMenu(Model $record, string $routeKey = 'show'): void
    {
        $languageMenu = collect(app(LanguageRepository::class)->locales())
            ->reject(fn($locale) => (string)$locale === app()->getLocale())
            ->mapWithKeys(function ($locale) use ($record, $routeKey) {
                $languagePage = $this->pagesService->getLanguagePage($locale);
                $relatedPage = $this->pagesService->getRelatedPage($locale);

                if ($languagePage?->getKey() === $relatedPage?->getKey()) {
                    return [$locale => $languagePage?->getUrl()];
                }

                return [
                    $locale => $relatedPage?->getUrl($routeKey, [$record->getTranslation('slug', $locale)])
                ];
            })
            ->toArray();

        view()->share('languageMenu', $languageMenu);
    }
}
