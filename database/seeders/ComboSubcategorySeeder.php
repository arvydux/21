<?php

namespace Database\Seeders;

use App\Models\OtherProduct;

class ComboSubcategorySeeder extends NumberedDrinkSeeder
{
    protected function subcategoryName(): string
    {
        return 'Komplektai';
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
            ['number' => 1, 'name' => 'Čeburekas su kiauliena ir sriuba', 'price' => 6.90],
            ['number' => 2, 'name' => 'Čeburekas su kiauliena + vištienos sultinys', 'price' => 6.90],
            ['number' => 3, 'name' => 'Pietūs (11:00 – 14:00)', 'price' => 6.90],
            ['number' => 4, 'name' => 'Pietūs su sriuba (11:00 – 14:00)', 'price' => 6.90],
        ];
    }
}
