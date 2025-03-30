<?php

namespace Database\Seeders;


use App\Models\Drink;
use Illuminate\Database\Seeder;

class DrinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Pepsi', 'price' => 2.0,
            ],
            [
                'name' => 'Mirinda', 'price' => 2.0,
            ],
            [
                'name' => '7Up', 'price' => 2.0,
            ],
        ];

        Drink::insert($products);
    }
}
