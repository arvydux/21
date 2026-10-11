<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Drink;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

abstract class NumberedDrinkSeeder extends Seeder
{
    /**
     * Name of the drinks subcategory to fill.
     */
    abstract protected function subcategoryName(): string;

    /**
     * Products as listed on the paper menu; the number may be null when the menu
     * has none, and extra attributes (e.g. a Čeburekai "sign") are stored as given.
     *
     * @return array<int, array{number: ?int, name: string, price: float, sign?: string}>
     */
    abstract protected function products(): array;

    /**
     * Component name of the category that owns the subcategory.
     */
    protected function categoryComponentName(): string
    {
        return 'drinks';
    }

    /**
     * Product model of that category.
     *
     * @return class-string<Model>
     */
    protected function productModel(): string
    {
        return Drink::class;
    }

    /**
     * Add the subcategory to the category and fill it with the drinks. A drink
     * already in this subcategory is left as is, so re-running never
     * overwrites admin edits. Otherwise an existing drink with the same name
     * and no number yet is moved into it instead of being duplicated, and if
     * there is none a new one is created (the same dish may then appear in
     * several subcategories, each with its own number).
     */
    public function run(): void
    {
        $category = Category::where('component_name', $this->categoryComponentName())->first();

        if ($category === null) {
            return;
        }

        $subcategory = Subcategory::firstOrCreate(
            ['category_id' => $category->id, 'name' => $this->subcategoryName()],
            ['position' => (Subcategory::where('category_id', $category->id)->max('position') ?? -1) + 1],
        );

        $model = $this->productModel();
        $position = ($model::max('position') ?? -1) + 1;

        foreach ($this->products() as $product) {
            if ($model::where('name', $product['name'])->where('subcategory_id', $subcategory->id)->exists()) {
                continue;
            }

            $drink = $model::where('name', $product['name'])->whereNull('number')->first();

            if ($drink !== null) {
                $drink->update([...$product, 'position' => $position++, 'subcategory_id' => $subcategory->id]);

                continue;
            }

            $model::create([...$product, 'show' => true, 'position' => $position++, 'subcategory_id' => $subcategory->id]);
        }
    }
}
