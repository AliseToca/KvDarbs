<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeasurementTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $measurmentTypes = [
            ['name' => 'svars'],
            ['name' => 'tilpums'],
            ['name' => 'skaits'],
        ];

        DB::table('measurement_types')->insert($measurmentTypes);
    }
}
