<?php

namespace Database\Seeders;

use App\Models\Ceburek;
use Illuminate\Database\Seeder;

class CeburekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Sūris + grybai', 'price' => 4.50, 'sign' => 'SG',
            ],
            [
                'name' => '2-jų rūšių sūris', 'price' => 4.50, 'sign' => '2S',
            ],
            [
                'name' => 'Saliamis + sūris', 'price' => 4.50, 'sign' => 'SS',
            ],
            [
                'name' => 'Čeburėkas su kiauliena', 'price' => 4.50, 'sign' => 'K',
            ],
            [
                'name' => 'Čeburėkas su jautiena', 'price' => 5.20, 'sign' => 'J',
            ],
            [
                'name' => 'Čeburėkas su vištiena', 'price' => 5.20, 'sign' => 'V',
            ],
            [
                'name' => 'Čeburėkas Mix', 'price' => 5.20, 'sign' => 'M',
            ],
            [
                'name' => 'Čeburėkas su aviena', 'price' => 5.90, 'sign' => 'A',
            ],
            [
                'name' => 'Čeburėkas Maišytas', 'price' => 5.90, 'sign' => 'MA',
            ],
            [
                'name' => 'Čeburėkas Šefo', 'price' => 6.80, 'sign' => 'Š',
            ],
            [
                'name' => 'Čeburėkas Gurmaniškas', 'price' => 6.80, 'sign' => 'G',
            ],
            [
                'name' => 'Čeburėkas Pachmo', 'price' => 7.50, 'sign' => 'P',
            ],
        ];

        Ceburek::insert($products);
    }
}
