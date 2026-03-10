<?php

namespace Database\Seeders;

use App\Filament\Templates\HouseholdTemplate;
use App\Filament\Templates\LanguageTemplate;
use App\Filament\Templates\RecipeTemplate;
use App\Filament\Templates\ShoppingListTemplate;
use CubeAgency\FilamentPageManager\Models\Page;
use Illuminate\Database\Seeder;
use Waavi\Translation\Repositories\LanguageRepository;

class CubeInstallSeeder extends Seeder
{
    public function __construct(
        protected LanguageRepository $languageRepository
    ) {
    }

    public function run(): void
    {
        if ($this->languageRepository->getModel()->all()->isEmpty()) {
            $this->languageRepository->create([
                'locale' => 'lv',
                'name' => 'Latvian',
            ]);
        }

        //Noklusējuma lapu izveide
        if (Page::all()->isEmpty()) {
            Page::create([
                'name' => 'LV',
                'slug' => 'lv',
                'template' => LanguageTemplate::class,
                'content' => [
                    'locale' => 'lv',
                    'site_name' => 'MājasReceptes',
                ],
                'activate_at' => now(),
            ]);

            Page::create([
                'parent_id' => 1,
                'name' => 'Mājsaimniecība',
                'slug' => 'majsaimnieciba',
                'template' => HouseholdTemplate::class,
                'activate_at' => now(),
            ]);

            Page::create([
                'parent_id' => 1,
                'name' => 'Receptes',
                'slug' => 'receptes',
                'template' => RecipeTemplate::class,
                'activate_at' => now(),
            ]);

            Page::create([
                'parent_id' => 1,
                'name' => 'Iepirkšanās saraksts',
                'slug' => 'iepirksanas-saraksts',
                'template' => ShoppingListTemplate::class,
                'activate_at' => now(),
            ]);

            $this->call(MenuSeeder::class);
        }
    }
}
