<?php

namespace Database\Seeders;

use App\Models\AlcoholicDrink;

class CiderSeeder extends NumberedDrinkSeeder
{
    protected function subcategoryName(): string
    {
        return 'Sidras';
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
            ['number' => 1, 'name' => 'Vilkmergės CRAFT sidras (400 ml)', 'price' => 4.60],
        ];
    }
}
