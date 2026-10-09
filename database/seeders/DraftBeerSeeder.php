<?php

namespace Database\Seeders;

use App\Models\AlcoholicDrink;

class DraftBeerSeeder extends NumberedDrinkSeeder
{
    protected function subcategoryName(): string
    {
        return 'Pilstomas alus';
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
            ['number' => 1, 'name' => '„Vilkmergės Premium lager“ (400 ml)', 'price' => 4.40],
            ['number' => 2, 'name' => '„Vilkmergės“ tamsusis (400 ml)', 'price' => 4.40],
        ];
    }
}
