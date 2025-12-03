<?php

namespace App\Http\Controllers;
use App\Models\Menu;
use App\Services\PagesService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FrontController extends Controller
{
    protected function sharedProps(): void
    {
        Inertia::share([
            'headerMenu' => fn() => Menu::with('items.page')->where('type', 'header')->first(),
            'footerMenu' => fn() => Menu::with('items.page')->where('type', 'footer')->first(),
            'languagePage' => fn() => app(PagesService::class)->getLanguagePage()->only(['content', 'slug']),
        ]);
    }
}
