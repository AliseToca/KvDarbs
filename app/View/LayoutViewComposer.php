<?php

namespace App\View;

use App\Repositories\LanguageRepository;
use App\Services\CookiesService;
use App\Services\ImageService;
use App\Services\PagesService;
use App\Settings\GeneralSettings;
use CubeAgency\FilamentPageManager\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Http\Request;
use Illuminate\Support\Fluent;
use InvalidArgumentException;

final class LayoutViewComposer
{
    protected string $locale;

    protected ?Page $languagePage;

    protected array $action;

    public function __construct(
        protected CookiesService $cookiesService,
        protected LanguageRepository $languageRepository,
        protected GeneralSettings $settings,
        protected PagesService $pagesService,
        protected ImageService $imageService,
        protected Request $request,
    ) {
        $this->locale = app()->getLocale();
        $this->languagePage = $pagesService->getLanguagePage();
        $this->action = optional($request->route())->getAction();
    }

    /**
     * @throws InvalidArgumentException
     * @throws MassAssignmentException
     */
    public function compose(View $view): void
    {
        $view->with([
            'locale' => $this->locale,
            'settings' => $this->settings,
            'metaData' => $this->getMetaData(),
            'cookiesPage' => $this->cookiesService->getCookiesPage(),
            'isCookieNoticeActive' => CookiesService::isCookieNoticeActive(),
            'cookieGroups' => CookiesService::getCookieGroups(),
        ]);

        if (!$view->offsetExists('languageMenu')) {
            $view->with('languageMenu', $this->getLanguageMenu());
        }
    }

    public function getLanguageMenu(): array
    {
        return collect($this->languageRepository->locales())
            ->reject(fn($locale) => (string)$locale === $this->locale)
            ->mapWithKeys(function ($locale) {
                return [$locale => $this->pagesService->getRelatedPage($locale)?->getUrl()];
            })
            ->filter()
            ->toArray();
    }

    public function getMetaData(): Fluent
    {
        if (!empty($this->languagePage)
            && (request()->path() === '/' || $this->action['pageId'] === $this->languagePage->id)) {
            return new Fluent([
                'name' => $this->languagePage->getAttribute('name'),
                'description' => $this->languagePage->meta['description'] ?? null,
                'keywords' => $this->languagePage->meta['keywords'] ?? [],
                'path' => $this->languagePage->getUrl(),
                'image' => $this->imageService->getImageUrl($this->languagePage->metaImage, 'jpg') ?? null,
            ]);
        }

        $page = $this->pagesService->getPageById($this->action['pageId']);

        return new Fluent([
            'name' => $page?->getAttribute('name'),
            'description' => $page->meta['description'] ?? null,
            'keywords' => $page->meta['keywords'] ?? [],
            'path' => $page?->getUrl(),
            'image' => $this->imageService->getImageUrl($this->languagePage->metaImage, 'jpg') ?? null,
        ]);
    }
}
