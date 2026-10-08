<?php

namespace Database\Seeders;

use App\Models\PotatoTopping;
use Illuminate\Database\Seeder;

class PotatoToppingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $toppings = [
            ['name' => 'Grietinė', 'price' => 0.00, 'sign' => 'G', 'is_default' => true],
            ['name' => 'Spirgučiai ir grietinė', 'price' => 0.40, 'sign' => 'SG', 'is_default' => false],
            ['name' => 'Pievagrybių padažas', 'price' => 0.60, 'sign' => 'PP', 'is_default' => false],
        ];

        foreach ($toppings as $position => $topping) {
            PotatoTopping::create([...$topping, 'position' => $position]);
        }
    }
}
