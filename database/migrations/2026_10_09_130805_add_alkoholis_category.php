<?php

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the empty "Alkoholis" category with its
     * default "Visi" subcategory. A fresh database (no categories yet) is left
     * to the seeders. Idempotent and safe to re-run.
     */
    public function up(): void
    {
        if (Category::count() === 0 || Category::where('component_name', 'alkoholis')->exists()) {
            return;
        }

        $category = Category::create([
            'name' => 'Alkoholis',
            'component_name' => 'alkoholis',
            'position' => Category::max('position') + 1,
        ]);

        Subcategory::firstOrCreate(
            ['category_id' => $category->id, 'name' => 'Visi'],
            ['position' => 0],
        );
    }

    public function down(): void
    {
        // Data backfill only — the alcoholic_drinks table is dropped by its own migration.
    }
};
