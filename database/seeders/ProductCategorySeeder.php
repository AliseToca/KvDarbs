<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productCategories = [
            //TO DO : Add ALL product categories
            ['name' => 'Gaļa un zivis'],
            ['name' => 'Piena produkti'],
            ['name' => 'Dārzeņi'],
            ['name' => 'Augļi'],
            ['name' => 'Garšvielas'],
        ];

        DB::table('product_categories')->insert($productCategories);
    }
}
