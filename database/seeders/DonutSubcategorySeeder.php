<?php

namespace Database\Seeders;

use App\Models\Kepiniai;

class DonutSubcategorySeeder extends NumberedDrinkSeeder
{
    /**
     * Names the original seeder gave these products, keyed by their new name.
     *
     * @var array<string, string>
     */
    public const PREVIOUS_NAMES = [
        'Varškės spurga' => 'Spurga',
    ];

    protected function subcategoryName(): string
    {
        return 'Spurgos';
    }

    protected function categoryComponentName(): string
    {
        return 'kepiniai';
    }

    protected function productModel(): string
    {
        return Kepiniai::class;
    }

    protected function products(): array
    {
        return [
            ['number' => 1, 'name' => 'Varškės spurga', 'price' => 1.20],
        ];
    }
}
