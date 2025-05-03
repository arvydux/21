<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FreeNumbers;
use Illuminate\Database\Seeder;

class FreeNumbersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $number = [
            [
                'number' => 1,
            ],
        ];

        FreeNumbers::insert($number);
    }
}
