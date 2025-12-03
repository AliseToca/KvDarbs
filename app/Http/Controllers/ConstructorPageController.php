<?php

namespace App\Http\Controllers;

use App\Filament\Templates\ConstructorTemplate;
use CubeAgency\FilamentConstructor\Constructor\ConstructorBlockRenderer;
use CubeAgency\FilamentPageManager\Models\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConstructorPageController extends FrontController
{
    public function index(Request $request)
    {
        $this->sharedProps();

        $page = $this->loadPage($request);
        $renderer = new ConstructorBlockRenderer(config('filament-constructor.blocks'));

        return Inertia::render('ConstructorPage', [
            'page' => $page,
            'blocks' => json_decode($page->blocks) ?? [],
        ]);
    }

    protected function loadPage(Request $request): Model
    {
        $action = $this->getAction($request);

        return Page::query()
            ->select(['id', 'name', "content->blocks as blocks"])
            ->where('template', ConstructorTemplate::class)
            ->findOrFail($action['pageId']);
    }


}
