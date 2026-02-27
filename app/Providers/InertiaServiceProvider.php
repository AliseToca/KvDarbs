<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\Menu;
use App\Services\PagesService;
use Waavi\Translation\Repositories\LanguageRepository;
use App\Enums\Recipe\Visibility;
use Illuminate\Support\Facades\File;

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
            'translations' => function () {
                $locale = app()->getLocale();
                $path = lang_path($locale);

                $translations = [];

                foreach (File::files($path) as $file) {
                    $key = pathinfo($file, PATHINFO_FILENAME);
                    $translations[$key] = trans($key);
                }

                return $translations;
            },
            'headerMenu' => fn () => $this->getMenuItems('header'),
            'footerMenu' => fn () => $this->getMenuItems('footer'),
            'user' => fn () => auth()->user() ? [
                'name' => auth()->user()->name,
                'username' => auth()->user()->username,
                'avatar_src' => auth()->user()->avatar_src,
            ] : null,
            'languagePage' => fn () => app(PagesService::class)
                ->getLanguagePage()
                ->only(['content', 'slug']),
            'enums' => [
                'visibility' => collect(Visibility::cases())->map(fn($case) =>
                [
                    'id'   => $case->value,
                    'name' => $case->label(),
                ]),
            ],
            'flash' => [
                'success' => fn () => session('success'),
                'error' => fn () => session('error'),
            ],
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
