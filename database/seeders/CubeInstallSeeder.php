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
                'locale' => 'en',
                'name' => 'English',
            ]);
        }

        if (Page::all()->isEmpty()) {
            Page::create([
                'name' => 'EN',
                'slug' => 'en',
                'template' => LanguageTemplate::class,
                'content' => [
                    'locale' => 'en',
                ],
                'activate_at' => now(),
            ]);
        }
    }
}
