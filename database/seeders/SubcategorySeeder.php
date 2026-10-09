<?php

namespace Database\Seeders;

use App\Models\AlcoholicDrink;
use App\Models\Category;
use App\Models\Ceburek;
use App\Models\Drink;
use App\Models\Kibinai;
use App\Models\KoldunaiDish;
use App\Models\OtherProduct;
use App\Models\PotatoDish;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class SubcategorySeeder extends Seeder
{
    /**
     * Map each category's component_name to its product model class.
     *
     * @var array<string, class-string<Model>>
     */
    private array $componentModels = [
        'ceburekai' => Ceburek::class,
        'kibinai' => Kibinai::class,
        'drinks' => Drink::class,
        'other' => OtherProduct::class,
        'bulviniai' => PotatoDish::class,
        'koldunai' => KoldunaiDish::class,
        'alkoholis' => AlcoholicDrink::class,
    ];

    /**
     * Create a default "Visi" subcategory for every category and assign all
     * existing products (with no subcategory yet) to it, so nothing disappears
     * from the cashier once product buttons are filtered by subcategory.
     */
    public function run(): void
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
}
