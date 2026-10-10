<?php

namespace Database\Seeders;

use App\Models\OtherProduct;

class SoupSubcategorySeeder extends NumberedDrinkSeeder
{
    protected function subcategoryName(): string
    {
        return 'Sriubos';
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
            ['number' => 1, 'name' => 'Natūralus vištienos sultinys', 'price' => 2.50],
            ['number' => 2, 'name' => 'Dienos sriuba (perkant čebureką)', 'price' => 2.60],
            ['number' => 3, 'name' => 'Dienos sriuba', 'price' => 3.50],
            ['number' => 4, 'name' => 'Šaltibarščiai su bulvėmis', 'price' => 4.50],
        ];
    }
}
