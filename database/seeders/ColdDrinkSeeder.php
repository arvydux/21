<?php

namespace Database\Seeders;

class ColdDrinkSeeder extends NumberedDrinkSeeder
{
    protected function subcategoryName(): string
    {
        return 'Šalti gėrimai';
    }

    protected function products(): array
    {
        return [
            ['number' => 1, 'name' => 'Apelsinų sultys', 'price' => 2.50],
            ['number' => 2, 'name' => 'Obuolių sultys', 'price' => 2.50],
            ['number' => 3, 'name' => 'Pomidorų sultys', 'price' => 2.50],
            ['number' => 4, 'name' => 'Pepsi (330 ml)', 'price' => 1.80],
            ['number' => 5, 'name' => '7UP (330 ml)', 'price' => 1.80],
            ['number' => 6, 'name' => 'Mirinda (330 ml)', 'price' => 1.80],
            ['number' => 7, 'name' => 'Gira „Vilkmergės“ (400 ml)', 'price' => 2.80],
            ['number' => 8, 'name' => 'Pilstoma gira „TAURAS“ (500 ml)', 'price' => 3.30],
            ['number' => 9, 'name' => 'Gazuotas vanduo (500 ml)', 'price' => 1.20],
            ['number' => 10, 'name' => 'Negazuotas vanduo (500 ml)', 'price' => 1.20],
            ['number' => 11, 'name' => 'Energetinis gėrimas CULT (N18)', 'price' => 2.00],
            ['number' => 12, 'name' => 'Nealkoholinis alus „Vilkmergės“ (N18)', 'price' => 3.60],
        ];
    }
}
