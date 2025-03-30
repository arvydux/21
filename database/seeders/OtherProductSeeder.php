<?php

namespace Database\Seeders;


use App\Models\OtherProduct;
use Illuminate\Database\Seeder;

class OtherProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Other 1', 'price' => 1.8,
            ],
            [
                'name' => 'Other 12', 'price' => 2.0,
            ],
            [
                'name' => 'Other 123', 'price' => 2.2,
            ],
        ];

        OtherProduct::insert($products);
    }
}
