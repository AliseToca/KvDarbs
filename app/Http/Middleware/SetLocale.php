<?php

namespace App\Http\Middleware;

use App\Filament\Templates\LanguageTemplate;
use Closure;
use CubeAgency\FilamentPageManager\Models\Page;
use Illuminate\Http\Request;
use App\Repositories\LanguageRepository;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $this->detectCurrentLocaleFromRoute();

        return $next($request);
    }

    protected function detectCurrentLocaleFromRoute(): void
    {
        $firstSegment = request()->segment(1);

        $languagePage = Page::query()
            ->where('template', LanguageTemplate::class)
            ->where('slug', $firstSegment)
            ->whereNull('parent_id')
            ->first();

        if ($languagePage && !empty($languagePage->content['locale'])) {
            app()->setLocale($languagePage->content['locale']);
            request()->setLocale($languagePage->content['locale']);

            return;
        }

        $locales = app(LanguageRepository::class)->locales();
        if (in_array($firstSegment, $locales, true)) {
            app()->setLocale($firstSegment);
            request()->setLocale($firstSegment);

            return;
        }

        $fallbackLocale = config('app.locale');
        app()->setLocale($fallbackLocale);
        request()->setLocale($fallbackLocale);
    }
}
