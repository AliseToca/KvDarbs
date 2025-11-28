<?php

namespace App\Http\Controllers;

use App\Filament\Templates\ConstructorTemplate;
use CubeAgency\FilamentConstructor\Constructor\ConstructorBlockRenderer;
use CubeAgency\FilamentPageManager\Models\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConstructorPageController extends Controller
{
    public function index(Request $request): View
    {
        $page = $this->loadPage($request);
        $renderer = new ConstructorBlockRenderer(config('filament-constructor.blocks'));

        return view('controllers.constructor.index', [
            'page' => $page,
            'blocks' => $renderer->render(json_decode($page->blocks) ?? [])
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
