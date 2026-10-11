<?php

namespace Database\Seeders;

use App\Models\KoldunaiTopping;
use Illuminate\Database\Seeder;

class KoldunaiToppingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $toppings = [
            ['name' => 'Jelapenai', 'price' => 0.60, 'sign' => 'J'],
            ['name' => 'Kepinti svogūnų dribsniai', 'price' => 0.60, 'sign' => 'SD'],
        ];

        foreach ($toppings as $position => $topping) {
            KoldunaiTopping::create([...$topping, 'position' => $position]);
        }
    }
}
