<?php

namespace Database\Seeders;

use App\Models\AlcoholicDrink;

class WineSeeder extends NumberedDrinkSeeder
{
    protected function subcategoryName(): string
    {
        return 'Vynas';
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
            ['number' => 1, 'name' => 'Baltasis sausas vynas „COLPASSO“ (150 ml)', 'price' => 5.50],
            ['number' => 2, 'name' => 'Baltasis sausas vynas „COLPASSO“ (750 ml)', 'price' => 22.00],
            ['number' => 3, 'name' => 'Raudonasis sausas vynas „PRECISO“ (150 ml)', 'price' => 5.70],
            ['number' => 4, 'name' => 'Raudonasis sausas vynas „PRECISO“ (750 ml)', 'price' => 24.00],
            ['number' => 5, 'name' => 'Raudonas saldus vynas „KAGOR“ (150 ml)', 'price' => 5.20],
            ['number' => 6, 'name' => 'Raudonas saldus vynas „KAGOR“ (750 ml)', 'price' => 20.00],
            ['number' => 7, 'name' => 'Putojantis vynas „Escanza PROSECCO“ (150 ml)', 'price' => 5.50],
            ['number' => 8, 'name' => 'Putojantis vynas „Escanza PROSECCO“ (750 ml)', 'price' => 22.00],
            ['number' => 9, 'name' => 'Putojantis saldus vynas „BOSCA“ (150 ml)', 'price' => 3.90],
            ['number' => 10, 'name' => 'Putojantis saldus vynas „BOSCA“ (750 ml)', 'price' => 13.00],
            ['number' => 11, 'name' => 'Karštas vynas (150 ml)', 'price' => 5.40],
        ];
    }
}
