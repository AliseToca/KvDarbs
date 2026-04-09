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
            ['name' => 'Dārzeņi'],
            ['name' => 'Augļi'],
            ['name' => 'Garšvielas'],
            ['name' => 'Maize un milti'],
            ['name' => 'Eļļas un tauki'],
            ['name' => 'Konditorejas izstrādājumi'],
            ['name' => 'Pākšaugi'],
            ['name' => 'Rieksti un sēklas'],
            ['name' => 'Graudaugi un putraimi'],
            ['name' => 'Buljons un mērces'],
            ['name' => 'Sēnes'],
            ['name' => 'Dzērieni'],
        ];

        DB::table('product_categories')->insert($productCategories);
    }
}
