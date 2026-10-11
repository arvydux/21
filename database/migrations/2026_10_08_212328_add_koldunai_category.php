<?php

use App\Models\Category;
use App\Models\KoldunaiDish;
use App\Models\KoldunaiTopping;
use App\Models\Subcategory;
use Database\Seeders\KoldunaiDishSeeder;
use Database\Seeders\KoldunaiToppingSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the "Koldūnai bei bulvytės" category with
     * its default "Visi" subcategory, dishes and toppings. A fresh database (no
     * categories yet) is left to the seeders. Idempotent and safe to re-run.
     */
    public function up(): void
    {
        if (Category::count() === 0 || Category::where('component_name', 'koldunai')->exists()) {
            return;
        }

        $category = Category::create([
            'name' => 'Koldūnai bei bulvytės',
            'component_name' => 'koldunai',
            'position' => Category::max('position') + 1,
        ]);

        $defaultSubcategory = Subcategory::firstOrCreate(
            ['category_id' => $category->id, 'name' => 'Visi'],
            ['position' => 0],
        );

        if (KoldunaiDish::count() === 0) {
            (new KoldunaiDishSeeder)->run();
        }

        if (KoldunaiTopping::count() === 0) {
            (new KoldunaiToppingSeeder)->run();
        }

        KoldunaiDish::query()
            ->whereNull('subcategory_id')
            ->update(['subcategory_id' => $defaultSubcategory->id]);
    }

    public function down(): void
    {
        // Data backfill only — the koldunai tables are dropped by their own migrations.
    }
};
