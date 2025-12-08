<?php

namespace App\Http\Controllers;
use App\Models\Menu;
use App\Services\PagesService;
use Waavi\Translation\Repositories\LanguageRepository;
use Inertia\Inertia;

class FrontController extends Controller
{
    protected function sharedProps(): void
    {
        $languageRepository = app(LanguageRepository::class);

        Inertia::share([
            'locale' => app()->getLocale(),
            'headerMenu' => $this->getMenuItems('header'),
            'footerMenu' => $this->getMenuItems('footer'),
            'availableLocales' => $languageRepository->availableLocales(),
            'languagePage' => fn() => app(PagesService::class)->getLanguagePage()->only(['content', 'slug']),
        ]);
    }

    protected function getMenuItems(string $type){
        return Menu::with('items.page')->where('type', $type)->first();
    }
}
