<?php

namespace Database\Seeders;

use App\Models\Topping;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ToppingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $toppings = [
            [
                'name' => 'Sūriu', 'price' => 1.0, 'sign' => 'S',
            ],
            [
                'name' => 'Grybai', 'price' => 0.6, 'sign' => 'G',
            ],
            [
                'name' => 'Jelapenai', 'price' => 0.6, 'sign' => 'J',
            ],
            [
                'name' => '2-jų rūšių sūris', 'price' => 1.2, 'sign' => '2S',
            ],
            [
                'name' => 'Daugiau mėsos', 'price' => 4.0, 'sign' => '2S',
            ],
        ];

        Topping::insert($toppings);
    }
}
