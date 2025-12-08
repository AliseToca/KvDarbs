<?php

namespace App\Http\Controllers;

use App\Services\PagesService;
use CubeAgency\FilamentConstructor\Constructor\ConstructorBlockRenderer;
use CubeAgency\FilamentPageManager\Models\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;

class LanguagePageController extends Controller
{
    public function index(Request $request): Response
    {
        $page = $this->loadPage($request);
        $renderer = new ConstructorBlockRenderer(config('filament-constructor.language_blocks'));

        return Inertia::render('Language/Index', [
            'page' => $page,
            'content' => $page->content,
            'blocks' => $renderer->render(json_decode($page->blocks) ?? []),
        ]);
    }

    protected function loadPage(Request $request): Model
    {
        return resolve(PagesService::class)->getLanguagePage();
    }
}
