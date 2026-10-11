<?php

namespace Database\Seeders;

use App\Models\OtherProduct;

class SnackSubcategorySeeder extends NumberedDrinkSeeder
{
    protected function subcategoryName(): string
    {
        return 'Užkandžiai';
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
            ['number' => 1, 'name' => 'Gruzdintos bulvytės „FRI“ su pomidorų padažu', 'price' => 3.50],
            ['number' => 2, 'name' => 'Svogūnų žiedai 12 vnt. su česnakiniu padažu', 'price' => 4.50],
            ['number' => 3, 'name' => 'Kalmarų žiedai 12 vnt. su česnakiniu padažu', 'price' => 5.50],
            ['number' => 4, 'name' => 'Vištienos filė piršteliai 8 vnt. su pikantišku padažu', 'price' => 6.50],
            ['number' => 5, 'name' => 'Kepta duona sūrio padaže', 'price' => 5.50],
            ['number' => 6, 'name' => 'Kepta duona su sūrio padažu (atskirai)', 'price' => 5.50],
            ['number' => 7, 'name' => 'Gruzdinti naminiai koldūnai su mėsa ir pikantišku padažu', 'price' => 6.50],
            ['number' => 8, 'name' => 'Užkandžių rinkinys', 'price' => 18.50],
        ];
    }
}
