<?php

namespace Database\Seeders;

use App\Models\Kepiniai;

class KibinaiSubcategorySeeder extends NumberedDrinkSeeder
{
    /**
     * Names the original seeder gave these products, keyed by their new name.
     *
     * @var array<string, string>
     */
    public const PREVIOUS_NAMES = [
        'Kibinas su vištiena' => 'Kibinas su vistiena',
    ];

    protected function subcategoryName(): string
    {
        return 'Kibinai';
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
            ['number' => 1, 'name' => 'Kibinas su kiauliena', 'price' => 2.20],
            ['number' => 2, 'name' => 'Kibinas su kopūstais', 'price' => 2.00],
            ['number' => 3, 'name' => 'Kibinas su varške ir persikais', 'price' => 2.20],
            ['number' => 4, 'name' => 'Kibinas su vištiena', 'price' => 2.40],
            ['number' => 5, 'name' => 'Kibinas su kiauliena ir sūriu', 'price' => 2.50],
        ];
    }
}
