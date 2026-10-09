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
     * Drinks as listed on the paper menu; the number may be null when the menu has none.
     *
     * @return array<int, array{number: ?int, name: string, price: float}>
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
     * Add the subcategory to the category and fill it with the drinks. An
     * existing drink with the same name, no number yet and outside this
     * subcategory is moved into it instead of being duplicated; any other
     * existing drink is left as is, so re-running never overwrites admin edits.
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
            $drink = $model::where('name', $product['name'])->first();

            if ($drink === null) {
                $model::create([...$product, 'show' => true, 'position' => $position++, 'subcategory_id' => $subcategory->id]);

                continue;
            }

            if ($drink->number === null && $drink->subcategory_id !== $subcategory->id) {
                $drink->update([...$product, 'subcategory_id' => $subcategory->id]);
            }
        }
    }
}
