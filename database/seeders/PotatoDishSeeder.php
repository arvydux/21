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
            ['number' => 1, 'name' => 'Žemaičių blynai su kiauliena', 'half_price' => 4.00, 'price' => 7.40, 'sign' => 'ZK'],
            ['number' => 2, 'name' => 'Žemaičių blynai su vištiena', 'half_price' => 4.20, 'price' => 7.60, 'sign' => 'ZV'],
            ['number' => 3, 'name' => 'Žemaičių blynai su pievagrybiais', 'half_price' => 4.20, 'price' => 7.60, 'sign' => 'ZP'],
            ['number' => 4, 'name' => 'Bulvinukai su sūriu', 'half_price' => 4.20, 'price' => 7.60, 'sign' => 'BS'],
            ['number' => 5, 'name' => 'Bulviniai blynai su mėsa „Kėdainių“', 'half_price' => 4.00, 'price' => 7.40, 'sign' => 'BK'],
            ['number' => 6, 'name' => 'Traškūs bulviniai blynai be mėsos', 'half_price' => 3.20, 'price' => 5.00, 'sign' => 'TB'],
        ];

        foreach ($products as $position => $product) {
            PotatoDish::create([...$product, 'position' => $position]);
        }
    }
}
