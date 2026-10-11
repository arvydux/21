<?php

namespace Database\Seeders;

use App\Models\OtherProduct;

class KidsMenuSubcategorySeeder extends NumberedDrinkSeeder
{
    /**
     * Names the original seeder gave these products, keyed by their new name.
     *
     * @var array<string, string>
     */
    public const PREVIOUS_NAMES = [
        'Bulvių ir vištienos rutuliukai (be padažo)' => 'Bulvių ir vištienos rutuliukai',
    ];

    protected function subcategoryName(): string
    {
        return 'Vaikų meniu';
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
            ['number' => 2, 'name' => 'Bulvių ir vištienos rutuliukai (be padažo)', 'price' => 4.90],
            ['number' => 3, 'name' => 'Vištienos filė juostelės su bulvytėmis, agurkais ir pomidorų padažu', 'price' => 5.90],
            ['number' => 4, 'name' => 'Pieniškos dešrelės su bulvytėmis, agurkais ir pomidorų padažu', 'price' => 5.50],
        ];
    }
}
