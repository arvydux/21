<?php

namespace Database\Seeders;

use App\Models\OtherProduct;

class DessertSubcategorySeeder extends NumberedDrinkSeeder
{
    protected function subcategoryName(): string
    {
        return 'Desertai';
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
            ['number' => 1, 'name' => 'Žagarėliai su ledais ir padažu', 'price' => 5.50],
            ['number' => 2, 'name' => 'Ledai su vyšnių užpilu', 'price' => 3.90],
            ['number' => 3, 'name' => 'Ledai su šokolado užpilu', 'price' => 3.90],
            ['number' => 4, 'name' => 'Ledai su karamelės užpilu', 'price' => 3.90],
        ];
    }
}
