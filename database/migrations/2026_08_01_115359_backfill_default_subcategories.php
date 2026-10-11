<?php

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Map each category's component_name to its product table as it was named
     * when this migration was written (tables renamed later are not affected).
     *
     * @var array<string, string>
     */
    private array $componentTables = [
        'ceburekai' => 'cebureks',
        'kibinai' => 'kibinais',
        'drinks' => 'drinks',
        'other' => 'other_products',
    ];

    /**
     * On any deployment with existing products, give every category a default
     * "Visi" subcategory and move all products with no subcategory into it, so
     * nothing disappears from the cashier. Idempotent and safe to re-run.
     */
    public function up(): void
    {
        foreach (Category::all() as $category) {
            $defaultSubcategory = Subcategory::firstOrCreate(
                ['category_id' => $category->id, 'name' => 'Visi'],
                ['position' => 0],
            );

            $table = $this->componentTables[$category->component_name] ?? null;

            if ($table === null) {
                continue;
            }

            DB::table($table)
                ->whereNull('subcategory_id')
                ->update(['subcategory_id' => $defaultSubcategory->id]);
        }
    }

    public function down(): void
    {
        // Data backfill only — nothing to reverse.
    }
};
