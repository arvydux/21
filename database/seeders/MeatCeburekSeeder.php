<?php

namespace Database\Seeders;

use App\Models\Ceburek;

class MeatCeburekSeeder extends NumberedDrinkSeeder
{
    /**
     * Names the original CeburekSeeder gave these Čeburekai, keyed by their new name.
     *
     * @var array<string, string>
     */
    public const PREVIOUS_NAMES = [
        'Kiauliena' => 'Čeburėkas su kiauliena',
        'Vištiena' => 'Čeburėkas su vištiena',
        'Mix (kiaul. + jaut.)' => 'Čeburėkas Mix',
        'Jautiena' => 'Čeburėkas su jautiena',
        'Maišytas (jaut. + aviena)' => 'Čeburėkas Maišytas',
        'Aviena' => 'Čeburėkas su aviena',
    ];

    protected function subcategoryName(): string
    {
        return 'Su mėsa';
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
            ['number' => 1, 'name' => 'Kiauliena', 'price' => 5.50, 'sign' => 'K'],
            ['number' => 2, 'name' => 'Vištiena', 'price' => 5.90, 'sign' => 'V'],
            ['number' => 3, 'name' => 'Mix (kiaul. + jaut.)', 'price' => 5.90, 'sign' => 'M'],
            ['number' => 4, 'name' => 'Jautiena', 'price' => 6.50, 'sign' => 'J'],
            ['number' => 5, 'name' => 'Maišytas (jaut. + aviena)', 'price' => 6.90, 'sign' => 'MA'],
            ['number' => 6, 'name' => 'Aviena', 'price' => 7.20, 'sign' => 'A'],
        ];
    }
}
