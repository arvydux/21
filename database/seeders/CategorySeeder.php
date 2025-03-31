<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Čeburėkai', 'component_name' => 'ceburekai',
            ],
            [
                'name' => 'Kibinai', 'component_name' => 'kibinai',
            ],
            [
                'name' => 'Gėrimai', 'component_name' => 'drinks',
            ],
            [
                'name' => 'Kiti', 'component_name' => 'other',
            ],
        ];

        Category::insert($categories);
    }
}
