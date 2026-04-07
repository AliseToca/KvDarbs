<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recipeTypes = [
            ['name' => 'Brokastis'],
            ['name' => 'Pusdienas'],
            ['name' => 'Vakariņas'],
            ['name' => 'Launags'],
        ];

        DB::table('recipe_types')->insert($recipeTypes);
    }
}
