<?php

namespace Database\Seeders;

use App\Models\Ceburek;

class CheeseCeburekSeeder extends NumberedDrinkSeeder
{
    protected function subcategoryName(): string
    {
        return 'Su sūriu';
    }

    protected function categoryComponentName(): string
    {
        return 'ceburekai';
    }

    protected function productModel(): string
    {
        return Ceburek::class;
    }

    protected function products(): array
    {
        return [
            ['number' => 1, 'name' => 'Sūris + grybai', 'price' => 5.50, 'sign' => 'SG'],
            ['number' => 2, 'name' => '2-jų rūšių sūris', 'price' => 5.50, 'sign' => '2S'],
            ['number' => 3, 'name' => 'Saliamis + sūris', 'price' => 5.50, 'sign' => 'SS'],
        ];
    }
}
