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
            ['number' => 1, 'name' => 'Pepsi', 'price' => 1.80],
            ['number' => 2, 'name' => 'Mirinda', 'price' => 1.80],
            ['number' => 3, 'name' => '7UP', 'price' => 1.80],
            ['number' => 4, 'name' => 'Tauras gira', 'price' => 2.20],
            ['number' => 5, 'name' => 'Vilkmergės', 'price' => 2.50],
        ];
    }
}
