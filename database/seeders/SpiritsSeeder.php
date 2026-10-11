<?php

namespace Database\Seeders;

use App\Models\AlcoholicDrink;

class SpiritsSeeder extends NumberedDrinkSeeder
{
    protected function subcategoryName(): string
    {
        return 'Stiprieji alkoholiniai gėrimai';
    }

    protected function categoryComponentName(): string
    {
        return 'alkoholis';
    }

    protected function productModel(): string
    {
        return AlcoholicDrink::class;
    }

    protected function products(): array
    {
        return [
            ['number' => 1, 'name' => 'Degtinė LITHUANIAN AUKSINĖ (40 ml)', 'price' => 3.50],
            ['number' => 2, 'name' => 'Degtinė LITHUANIAN AUKSINĖ (500 ml)', 'price' => 30.00],
            ['number' => 3, 'name' => 'Viskis JACK DANIEL’S (40 ml)', 'price' => 4.60],
            ['number' => 4, 'name' => 'Viskis JACK DANIEL’S (500 ml)', 'price' => 40.00],
        ];
    }
}
