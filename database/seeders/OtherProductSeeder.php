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
                'name' => 'Bulviniai blynai', 'price' => 4.9,
            ],
            [
                'name' => 'Gruzdinti koldūnai', 'price' => 3.9,
            ],
            [
                'name' => 'Bulvių ir vištienos rutuliukai', 'price' => 4.9,
            ],
            [
                'name' => 'Svogūnų žiedai', 'price' => 3.9,
            ],
            [
                'name' => 'Bulvytės fri', 'price' => 2.8,
            ],
            [
                'name' => 'Bulvytės fri MIX', 'price' => 3.5,
            ],
            [
                'name' => 'Sūrio lazdelės', 'price' => 5.5,
            ],
            [
                'name' => 'Dienos sriuba', 'price' => 3.5,
            ],
            [
                'name' => 'Dienos sriuba (perkant čebureką)', 'price' => 2.6,
            ],
            [
                'name' => 'Natūralus vištienos sultinys', 'price' => 2.5,
            ],
        ];

        OtherProduct::insert($products);
    }
}
