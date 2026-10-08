<?php

namespace Database\Seeders;

use App\Models\PotatoDish;
use Illuminate\Database\Seeder;

class PotatoDishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Žemaičių blynai su kiauliena', 'half_price' => 4.00, 'price' => 7.40, 'sign' => 'ZK'],
            ['name' => 'Žemaičių blynai su vištiena', 'half_price' => 4.20, 'price' => 7.60, 'sign' => 'ZV'],
            ['name' => 'Žemaičių blynai su pievagrybiais', 'half_price' => 4.20, 'price' => 7.60, 'sign' => 'ZP'],
            ['name' => 'Bulvinukai su sūriu', 'half_price' => 4.20, 'price' => 7.60, 'sign' => 'BS'],
            ['name' => 'Bulviniai blynai su mėsa „Kėdainių“', 'half_price' => 4.00, 'price' => 7.40, 'sign' => 'BK'],
            ['name' => 'Traškūs bulviniai blynai be mėsos', 'half_price' => 3.20, 'price' => 5.00, 'sign' => 'TB'],
        ];

        foreach ($products as $position => $product) {
            PotatoDish::create([...$product, 'position' => $position]);
        }
    }
}
