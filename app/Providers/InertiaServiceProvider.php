<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\Menu;
use App\Services\PagesService;
use Waavi\Translation\Repositories\LanguageRepository;
class InertiaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Inertia::share([
            'locale' => fn () => app()->getLocale(),
            'translations' => fn () => [
                'auth' => trans('auth'),
                'validation' => trans('validation'),
                'recipe' => trans('recipe'),
                'household' => trans('household'),
                'button' => trans('button'),
                'fields' => trans('fields'),
            ],
            'headerMenu' => fn () => $this->getMenuItems('header'),
            'footerMenu' => fn () => $this->getMenuItems('footer'),
            'auth' => fn () => ['user' => auth()->user()],
//            'availableLocales' => fn () => app(LanguageRepository::class)->availableLocales(),
            'languagePage' => fn () => app(PagesService::class)
                ->getLanguagePage()
                ->only(['content', 'slug']),
        ]);
    }

    protected function getMenuItems(string $type)
    {
        $currentLanguage = app(PagesService::class)->getLanguagePage();

        //Iegūst izvēlnes vienības atkarība no pašreizējās valodas un tipa (galvene/kājene)
        $menu = Menu::with('items.page')
            ->where('type', $type)
            ->whereHas('items.page', function($query) use ($currentLanguage) {
                $query->where('parent_id', $currentLanguage->id);
            })
            ->first();

        return $menu ? $menu->items : collect();
    }
}
