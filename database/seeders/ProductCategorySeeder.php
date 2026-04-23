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
            ['name' => 'Gaļa un zivis'],
            ['name' => 'Piena produkti un olas'],
            ['name' => 'Augļi un dārzeņi'],
            ['name' => 'Citi krājumi'],
        ];

        DB::table('product_categories')->insert($productCategories);
    }
}
