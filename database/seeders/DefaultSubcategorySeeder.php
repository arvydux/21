<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class DefaultSubcategorySeeder extends Seeder
{
    /**
     * Make sure every category has the default "Visi" subcategory as its first
     * tab, so the cashier opens each category with all of its products. The
     * other subcategories keep their order. Safe to re-run.
     */
    public function run(): void
    {
        foreach (Category::all() as $category) {
            $default = Subcategory::firstOrCreate(
                ['category_id' => $category->id, 'name' => Subcategory::DEFAULT_NAME],
                ['position' => 0],
            );

            $default->update(['position' => 0]);

            Subcategory::where('category_id', $category->id)
                ->whereKeyNot($default->id)
                ->orderBy('position')
                ->orderBy('id')
                ->get()
                ->each(fn (Subcategory $subcategory, int $index) => $subcategory->update(['position' => $index + 1]));
        }
    }
}
