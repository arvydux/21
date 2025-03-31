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
                'name' => 'Čeburėkas su kiauliena', 'price' => 3.9, 'sign' => 'K',
            ],
            [
                'name' => 'Čeburėkas su jautiena', 'price' => 4.5, 'sign' => 'J',
            ],
            [
                'name' => 'Čeburėkas su aviena', 'price' => 4.9, 'sign' => 'A',
            ],
            [
                'name' => 'Čeburėkas Mix', 'price' => 4.5, 'sign' => 'M',
            ],
            [
                'name' => 'Čeburėkas Šefo', 'price' => 6.2, 'sign' => 'Š',
            ],
            [
                'name' => '2-jų rūšių sūriu', 'price' => 4.0, 'sign' => '2S',
            ],
            [
                'name' => 'Saliamis + sūris', 'price' => 4.0, 'sign' => 'SS',
            ],
            [
                'name' => 'Sūris + grybai', 'price' => 4.0, 'sign' => 'SS',
            ],
            [
                'name' => 'Su kiauliena + sriuba', 'price' => 5.8, 'sign' => 'KS',
            ],
        ];

        Ceburek::insert($products);
    }
}
