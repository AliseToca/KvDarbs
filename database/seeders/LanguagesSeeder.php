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
        if ($this->languageRepository->getModel()->all()->isEmpty()) {
            $this->languageRepository->create([
                'locale' => 'en',
                'name' => 'English',
            ]);
        }
    }
}
