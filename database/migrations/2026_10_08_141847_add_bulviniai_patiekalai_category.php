<?php

use App\Models\Category;
use App\Models\PotatoDish;
use App\Models\PotatoTopping;
use App\Models\Subcategory;
use Database\Seeders\PotatoDishSeeder;
use Database\Seeders\PotatoToppingSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the "Bulviniai patiekalai" category with its
     * default "Visi" subcategory, dishes and sauces. A fresh database (no
     * categories yet) is left to the seeders. Idempotent and safe to re-run.
     */
    public function up(): void
    {
        if (Category::count() === 0 || Category::where('component_name', 'bulviniai')->exists()) {
            return;
        }

        $category = Category::create([
            'name' => 'Bulviniai patiekalai',
            'component_name' => 'bulviniai',
            'position' => Category::max('position') + 1,
        ]);

        $defaultSubcategory = Subcategory::firstOrCreate(
            ['category_id' => $category->id, 'name' => 'Visi'],
            ['position' => 0],
        );

        if (PotatoDish::count() === 0) {
            (new PotatoDishSeeder)->run();
        }

        if (PotatoTopping::count() === 0) {
            (new PotatoToppingSeeder)->run();
        }

        PotatoDish::query()
            ->whereNull('subcategory_id')
            ->update(['subcategory_id' => $defaultSubcategory->id]);
    }

    public function down(): void
    {
        // Data backfill only — the potato tables are dropped by their own migrations.
    }
};
