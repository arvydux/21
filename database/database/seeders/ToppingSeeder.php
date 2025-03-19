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
                'name' => 'Suris', 'price' => 0.5,
            ],
            [
                'name' => 'Jalapenai', 'price' => 0.6,
            ],
            [
                'name' => 'Grybai', 'price' => 0.7,
            ],
        ];

        Topping::insert($toppings);
    }
}
