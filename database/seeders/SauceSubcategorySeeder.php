<?php

namespace Database\Seeders;

use App\Models\OtherProduct;

class SauceSubcategorySeeder extends NumberedDrinkSeeder
{
    protected function subcategoryName(): string
    {
        return 'Padažai';
    }

    protected function categoryComponentName(): string
    {
        return 'other';
    }

    protected function productModel(): string
    {
        return OtherProduct::class;
    }

    protected function products(): array
    {
        return [
            ['number' => 1, 'name' => 'Česnakinis padažas', 'price' => 1.00],
            ['number' => 2, 'name' => 'Pikantiškas padažas', 'price' => 1.00],
            ['number' => 3, 'name' => 'Pomidorų padažas', 'price' => 1.00],
        ];
    }
}
