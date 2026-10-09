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
        'Šefo' => 'Čeburėkas Šefo',
        'Gurmaniškas' => 'Čeburėkas Gurmaniškas',
        'Pachmo' => 'Čeburėkas Pachmo',
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
            ['number' => 7, 'name' => 'Šefo', 'price' => 7.50, 'sign' => 'Š'],
            ['number' => 8, 'name' => 'Gurmaniškas', 'price' => 7.20, 'sign' => 'G'],
            ['number' => 9, 'name' => 'Pachmo', 'price' => 7.50, 'sign' => 'P'],
        ];
    }
}
