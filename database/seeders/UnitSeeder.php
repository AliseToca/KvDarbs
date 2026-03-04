<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MeasurementType;


class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        $types = MeasurementType::pluck('id', 'name');

        $units = [
            ['name' => 'g', 'conversion_factor' => 1, 'measurement_type_id' => $types['svars']],
            ['name' => 'kg', 'conversion_factor' => 1000, 'measurement_type_id' => $types['svars']],

            ['name' => 'ml', 'conversion_factor' => 1, 'measurement_type_id' => $types['tilpums']],
            ['name' => 'l', 'conversion_factor' => 1000, 'measurement_type_id' => $types['tilpums']],
            ['name' => 'tējk.', 'conversion_factor' => 5, 'measurement_type_id' => $types['tilpums']],
            ['name' => 'ēdmk.', 'conversion_factor' => 15, 'measurement_type_id' => $types['tilpums']],

            ['name' => 'gab', 'conversion_factor' => 1, 'measurement_type_id' => $types['skaits']],
        ];


        DB::table('units')->insert($units);
    }
}
