<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\Menu;
use App\Services\PagesService;
use Waavi\Translation\Repositories\LanguageRepository;
use App\Enums\Recipe\Visibility;

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
            'languagePage' => fn () => app(PagesService::class)
                ->getLanguagePage()
                ->only(['content', 'slug']),
            'enums' => [
                'visibility' => collect(Visibility::cases())->map(fn($case) =>
                [
                    'id'   => $case->value,
                    'name' => $case->label(),
                ]),
            ]
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
