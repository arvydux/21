<?php

namespace Database\Seeders;


use App\Models\SimpleProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Spurga', 'price' => 1.8,
            ],
            [
                'name' => 'Kibinas su kiauliena', 'price' => 2.0,
            ],
            [
                'name' => 'Kibinas su vistiena', 'price' => 2.2,
            ],
        ];

        SimpleProduct::insert($products);
    }
}
