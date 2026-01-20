<?php

namespace Database\Seeders;

use App\Filament\Templates\LanguageTemplate;
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

        if (Page::all()->isEmpty()) {
            Page::create([
                'name' => 'LV',
                'slug' => 'lv',
                'template' => LanguageTemplate::class,
                'content' => [
                    'locale' => 'lv',
                ],
                'activate_at' => now(),
            ]);
        }
    }
}
