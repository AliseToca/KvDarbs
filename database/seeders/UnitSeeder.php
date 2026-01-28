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
            ['name' => 'g', 'conversion_factor' => 1, 'measurement_type_id' => 1],
            ['name' => 'kg', 'conversion_factor' => 1000, 'measurement_type_id' => 1],

            //Tilpums
            ['name' => 'ml', 'conversion_factor' => 1, 'measurement_type_id' => 2],
            ['name' => 'l', 'conversion_factor' => 1000, 'measurement_type_id' => 2],
            ['name' => 'tējk.', 'conversion_factor' => 5, 'measurement_type_id' => 2],
            ['name' => 'ēdmk.', 'conversion_factor' => 15, 'measurement_type_id' => 2],

            //Skaits
            ['name' => 'gab', 'conversion_factor' => 1, 'measurement_type_id' => 3],
        ];

        DB::table('units')->insert($units);
    }
}
