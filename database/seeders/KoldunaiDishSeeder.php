<?php

namespace Database\Seeders;

use App\Models\KoldunaiDish;
use Illuminate\Database\Seeder;

class KoldunaiDishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['number' => 1, 'name' => 'Užkeptos bulvytės „FRI“ su pikantišku padažu ir sūriu', 'price' => 6.50, 'sign' => 'FS'],
            ['number' => 2, 'name' => 'Užkeptos bulvytės „FRI“ su pikantišku padažu, kepta šonine ir sūriu', 'price' => 7.50, 'sign' => 'FŠ'],
            ['number' => 3, 'name' => 'Užkepti gruzdinti koldūnai su padažu ir sūriu', 'price' => 7.50, 'sign' => 'KS'],
            ['number' => 4, 'name' => 'Užkepti gruzdinti koldūnai su kepta šonine, padažu ir sūriu', 'price' => 8.50, 'sign' => 'KŠ'],
        ];

        foreach ($products as $position => $product) {
            KoldunaiDish::create([...$product, 'position' => $position]);
        }
    }
}
