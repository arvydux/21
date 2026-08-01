<?php

use App\Models\Category;
use App\Models\Ceburek;
use App\Models\Drink;
use App\Models\Kibinai;
use App\Models\OtherProduct;
use App\Models\Subcategory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Map each category's component_name to its product model class.
     *
     * @var array<string, class-string<\Illuminate\Database\Eloquent\Model>>
     */
    private array $componentModels = [
        'ceburekai' => Ceburek::class,
        'kibinai' => Kibinai::class,
        'drinks' => Drink::class,
        'other' => OtherProduct::class,
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

            $modelClass = $this->componentModels[$category->component_name] ?? null;

            if ($modelClass === null) {
                continue;
            }

            $modelClass::query()
                ->whereNull('subcategory_id')
                ->update(['subcategory_id' => $defaultSubcategory->id]);
        }
    }

    public function down(): void
    {
        // Data backfill only — nothing to reverse.
    }
};
