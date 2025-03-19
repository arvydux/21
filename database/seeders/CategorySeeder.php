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
                'name' => 'Čeburekai', 'component_name' => 'ceburekai',
            ],
            [
                'name' => 'Kibinai', 'component_name' => 'kibinai',
            ],
            [
                'name' => 'Gerimai', 'component_name' => 'gerimai',
            ],
            [
                'name' => 'Kiti', 'component_name' => 'kiti',
            ],
        ];

        Category::insert($categories);
    }
}
