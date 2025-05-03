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
            [
                'name' => 'Mangų ir apelsinų kokteilis ', 'price' => 3.5,
            ],
        ];

        Drink::insert($products);
    }
}
