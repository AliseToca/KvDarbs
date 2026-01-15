<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        $units = [
            //Svars
            ['name' => 'g', 'type' => 'weight', 'conversion_factor' => 1],
            ['name' => 'kg', 'type' => 'weight', 'conversion_factor' => 1000],

            //Tilpums
            ['name' => 'ml', 'type' => 'volume', 'conversion_factor' => 1],
            ['name' => 'l', 'type' => 'volume', 'conversion_factor' => 1000],

            //Skaits
            ['name' => 'pc', 'type' => 'count', 'conversion_factor' => 1],
            ['name' => 'tsp', 'type' => 'volume', 'conversion_factor' => 5],
            ['name' => 'tbsp', 'type' => 'volume', 'conversion_factor' => 15],
        ];

        DB::table('units')->insert($units);
    }
}
