<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PotatoDish;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

abstract class PotatoDishSubcategorySeeder extends Seeder
{
    /**
     * Name of the "Bulviniai patiekalai" subcategory to create.
     */
    abstract protected function subcategoryName(): string;

    /**
     * Existing dishes that belong in it, with their number inside the subcategory.
     *
     * @return array<string, int>
     */
    abstract public function dishes(): array;

    /**
     * Add the subcategory to "Bulviniai patiekalai", move the dishes into it
     * and number them from 1, keeping their prices. Once every dish has a
     * subcategory of its own, the empty default "Visi" one is removed. Safe to
     * re-run.
     */
    public function run(): void
    {
        $category = Category::where('component_name', 'bulviniai')->first();

        if ($category === null) {
            return;
        }

        $subcategory = Subcategory::firstOrCreate(
            ['category_id' => $category->id, 'name' => $this->subcategoryName()],
            ['position' => (Subcategory::where('category_id', $category->id)->max('position') ?? -1) + 1],
        );

        foreach ($this->dishes() as $name => $number) {
            PotatoDish::where('name', $name)->update(['subcategory_id' => $subcategory->id, 'number' => $number]);
        }

        $visi = Subcategory::where('category_id', $category->id)->where('name', 'Visi')->first();

        if ($visi !== null && ! PotatoDish::where('subcategory_id', $visi->id)->exists()) {
            $visi->delete();
        }
    }
}
