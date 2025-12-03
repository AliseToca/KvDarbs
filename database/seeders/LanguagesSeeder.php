<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Waavi\Translation\Repositories\LanguageRepository;

class LanguagesSeeder extends Seeder
{
    public function __construct(protected LanguageRepository $languageRepository)
    {
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $languages = [
            ['locale' => 'en', 'name' => 'English'],
            ['locale' => 'lv', 'name' => 'Latvian'],
        ];

        foreach ($languages as $lang) {
            $this->languageRepository->getModel()->updateOrCreate(
                ['locale' => $lang['locale']],
                ['name' => $lang['name']]
            );
        }
    }

}
