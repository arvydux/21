<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Drink;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

abstract class NumberedDrinkSeeder extends Seeder
{
    /**
     * Name of the drinks subcategory to fill.
     */
    abstract protected function subcategoryName(): string;

    /**
     * Drinks as numbered on the paper menu.
     *
     * @return array<int, array{number: int, name: string, price: float}>
     */
    abstract protected function products(): array;

    /**
     * Add the subcategory to the drinks category and fill it with the
     * numbered drinks. An existing drink with the same name and no number yet
     * is moved into the subcategory instead of being duplicated; numbered
     * drinks are left as is, so re-running never overwrites admin edits.
     */
    public function run(): void
    {
        $category = Category::where('component_name', 'drinks')->first();

        if ($category === null) {
            return;
        }

        $subcategory = Subcategory::firstOrCreate(
            ['category_id' => $category->id, 'name' => $this->subcategoryName()],
            ['position' => (Subcategory::where('category_id', $category->id)->max('position') ?? -1) + 1],
        );

        $position = (Drink::max('position') ?? -1) + 1;

        foreach ($this->products() as $product) {
            $drink = Drink::where('name', $product['name'])->first();

            if ($drink === null) {
                Drink::create([...$product, 'show' => true, 'position' => $position++, 'subcategory_id' => $subcategory->id]);

                continue;
            }

            if ($drink->number === null) {
                $drink->update([...$product, 'subcategory_id' => $subcategory->id]);
            }
        }
    }
}
