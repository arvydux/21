<?php

use App\Models\Category;
use App\Models\Drink;
use App\Models\Subcategory;
use Database\Seeders\ColdDrinkSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Replace every drink in "Šalti gėrimai" with the current paper menu, one
     * record per drink. Other drink subcategories are left untouched.
     */
    public function up(): void
    {
        $category = Category::where('component_name', 'drinks')->first();

        if ($category === null) {
            return;
        }

        $subcategory = Subcategory::where('category_id', $category->id)->where('name', 'Šalti gėrimai')->first();

        if ($subcategory !== null) {
            Drink::where('subcategory_id', $subcategory->id)->delete();
        }

        (new ColdDrinkSeeder)->run();
    }

    public function down(): void
    {
        // Data replacement only — the previous drinks are not restored.
    }
};
