<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LanguagesSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(ProductCategorySeeder::class);
        $this->call(MeasurementTypeSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(RecipeTypeSeeder::class);
    }
}
