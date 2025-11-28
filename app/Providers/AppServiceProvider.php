<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Role;
use App\Observers\PageObserver;
use App\Policies\LanguagePolicy;
use App\Policies\MediaPolicy;
use App\Policies\RedirectPolicy;
use App\Policies\RolePolicy;
use App\Policies\TranslationPolicy;
use App\Repositories\LanguageRepository;
use App\View\LayoutViewComposer;
use Awcodes\Curator\Models\Media;
use CubeAgency\FilamentRedirects\Models\Redirect;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use SolutionForest\FilamentTranslateField\Facades\FilamentTranslateField;
use Waavi\Translation\Models\Language;
use Waavi\Translation\Models\Translation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentTranslateField::defaultLocales(app(LanguageRepository::class)->locales());
        View::composer('layouts/main', LayoutViewComposer::class);
        Page::observe(PageObserver::class);

        // Only package policies need to be registered.
        Gate::policy(Language::class, LanguagePolicy::class);
        Gate::policy(Media::class, MediaPolicy::class);
        Gate::policy(Redirect::class, RedirectPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Translation::class, TranslationPolicy::class);
        Vite::useBuildDirectory('front');
        View::share('svgSpriteUrl', \App\Helpers\SvgSprite::getSpriteUrl());
    }
}
