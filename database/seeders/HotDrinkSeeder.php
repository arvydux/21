<?php

namespace Database\Seeders;

class HotDrinkSeeder extends NumberedDrinkSeeder
{
    protected function subcategoryName(): string
    {
        return 'Karšti gėrimai';
    }

    protected function products(): array
    {
        return [
            ['number' => 1, 'name' => 'Juoda/žalia/vaisinė arbata iš pakelio', 'price' => 1.20],
            ['number' => 2, 'name' => 'Plikyta arbata juoda/žalia/vaisinė', 'price' => 3.50],
            ['number' => 3, 'name' => 'Pasiflorų arbata', 'price' => 4.50],
            ['number' => 4, 'name' => 'Aviečių arbata', 'price' => 4.50],
            ['number' => 5, 'name' => 'Šaltalankių-imbierų arbata', 'price' => 4.50],
            ['number' => 6, 'name' => 'Kava juoda', 'price' => 2.20],
            ['number' => 7, 'name' => 'Kava Espresso', 'price' => 2.20],
            ['number' => 8, 'name' => 'Kava Latte', 'price' => 2.50],
            ['number' => 9, 'name' => 'Kava Cappuccino', 'price' => 2.50],
            ['number' => 10, 'name' => 'Kava be kofeino', 'price' => 2.50],
        ];
    }
}
