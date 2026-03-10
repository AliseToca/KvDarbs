<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use CubeAgency\FilamentPageManager\Models\Page;
use Illuminate\Database\Seeder;
use Waavi\Translation\Repositories\LanguageRepository;
use App\Filament\Templates\HouseholdTemplate;
use App\Filament\Templates\RecipeTemplate;
use App\Filament\Templates\ShoppingListTemplate;

class MenuSeeder extends Seeder
{
    public function __construct(
        protected LanguageRepository $languageRepository
    ) {}

    public function run(): void
    {
        $language = $this->languageRepository->getModel()->where('locale', 'lv')->first();

        $menu = Menu::firstOrCreate(
            ['name' => 'Header', 'type' => 'header'],
            ['language_id' => $language?->id]
        );

        $pages = Page::whereIn('template', [
            HouseholdTemplate::class,
            RecipeTemplate::class,
            ShoppingListTemplate::class,
        ])->get();

        foreach ($pages as $index => $page) {
            MenuItem::firstOrCreate(
                ['menu_id' => $menu->id, 'page_id' => $page->id],
                ['sort_order' => $index + 1]
            );
        }
    }
}
