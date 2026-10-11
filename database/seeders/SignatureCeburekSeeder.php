<?php

namespace Database\Seeders;

use App\Models\Ceburek;

class SignatureCeburekSeeder extends NumberedDrinkSeeder
{
    /**
     * Names the original CeburekSeeder gave these Čeburekai, keyed by their new name.
     *
     * @var array<string, string>
     */
    public const PREVIOUS_NAMES = [
        'Šefo' => 'Čeburėkas Šefo',
        'Gurmaniškas' => 'Čeburėkas Gurmaniškas',
        'Pachmo' => 'Čeburėkas Pachmo',
    ];

    protected function subcategoryName(): string
    {
        return 'Firminis';
    }

    protected function categoryComponentName(): string
    {
        return 'ceburekai';
    }

    protected function productModel(): string
    {
        return Ceburek::class;
    }

    /**
     * @return array<int, array{number: ?int, name: string, price: float, sign?: string}>
     */
    public function products(): array
    {
        return [
            ['number' => 1, 'name' => 'Šefo', 'price' => 7.50, 'sign' => 'Š'],
            ['number' => 2, 'name' => 'Gurmaniškas', 'price' => 7.20, 'sign' => 'G'],
            ['number' => 3, 'name' => 'Pachmo', 'price' => 7.50, 'sign' => 'P'],
        ];
    }
}
