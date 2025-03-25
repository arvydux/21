<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Topping;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Čeburekas su kiauliena', 'price' => 4.5, 'sign' => 'K',
            ],
            [
                'name' => 'Čeburekas su aviena', 'price' => 4.0, 'sign' => 'A',
            ],
            [
                'name' => 'Čeburekas su suriu', 'price' => 3.9, 'sign' => 'S',
            ],
            [
                'name' => 'Čeburekas su jautiena', 'price' => 5.0, 'sign' => 'J',
            ],
        ];

        Product::insert($products);
    }
}
