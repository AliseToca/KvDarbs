<?php

namespace App\Http\Controllers;

use App\Services\PagesService;
use CubeAgency\FilamentConstructor\Constructor\ConstructorBlockRenderer;
use CubeAgency\FilamentPageManager\Models\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LanguagePageController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        if (auth()->check()) {
            return Inertia::render('Profile/Edit');
        }

        $page = $this->loadPage($request);

        $renderer = new ConstructorBlockRenderer(
            config('filament-constructor.language_blocks')
        );

        $blocks = data_get($page->content, 'blocks', []);
        $blocks = json_decode(json_encode($blocks));

        return Inertia::render('Language/Index', [
            'page'    => $page,
            'content' => $page->content,
            'blocks'  => $renderer->render($blocks),
        ]);
    }


    protected function loadPage(Request $request): Model
    {
        return resolve(PagesService::class)->getLanguagePage();
    }
}
