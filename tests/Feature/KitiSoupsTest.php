<?php

use App\Livewire\Buttons\OtherProducts;
use App\Livewire\Menu\CreateForm\OtherProductsCreateForm;
use App\Livewire\Menu\Tables\OtherProductsTable;
use App\Models\Category;
use App\Models\OtherProduct;
use App\Models\Subcategory;
use Livewire\Livewire;

beforeEach(function () {
    $this->category = Category::create(['name' => 'Kiti', 'component_name' => 'other', 'position' => 3]);
    $this->visi = Subcategory::create(['name' => 'Visi', 'category_id' => $this->category->id, 'position' => 0]);
});

it('adds the soups to a "Sriubos" subcategory, moving the existing soup of the day', function () {
    $soup = OtherProduct::create(['name' => 'Dienos sriuba', 'price' => 3.5, 'show' => true, 'position' => 7, 'subcategory_id' => $this->visi->id]);
    $rings = OtherProduct::create(['name' => 'Svogūnų žiedai', 'price' => 3.9, 'show' => true, 'position' => 3, 'subcategory_id' => $this->visi->id]);

    $migration = require database_path('migrations/2026_10_10_194053_add_soup_subcategory_to_kiti.php');
    $migration->up();
    $migration->up();

    $soups = Subcategory::where('category_id', $this->category->id)->where('name', 'Sriubos')->sole();

    expect($soups->position)->toBe(1)
        ->and(OtherProduct::where('subcategory_id', $soups->id)->orderBy('position')->get()->map(fn (OtherProduct $product) => [$product->number, $product->name, (float) $product->price])->all())
        ->toBe([
            [1, 'Natūralus vištienos sultinys', 2.5],
            [2, 'Dienos sriuba (perkant čebureką)', 2.6],
            [3, 'Dienos sriuba', 3.5],
            [4, 'Šaltibarščiai su bulvėmis', 4.5],
        ])
        ->and($soup->fresh()->subcategory_id)->toBe($soups->id)
        ->and($rings->fresh()->subcategory_id)->toBe($this->visi->id)
        ->and(OtherProduct::count())->toBe(5);
});

it('shows the menu number on the Kiti button', function () {
    OtherProduct::create(['number' => 2, 'name' => 'Dienos sriuba (perkant čebureką)', 'price' => 2.6, 'show' => true, 'position' => 0, 'subcategory_id' => $this->visi->id]);

    Livewire::test(OtherProducts::class, ['subcategoryId' => $this->visi->id])
        ->assertSeeInOrder(['>2<', 'Dienos sriuba (perkant čebureką)'], escape: false);
});

it('creates and edits the menu number from the menu admin', function () {
    Livewire::test(OtherProductsCreateForm::class)
        ->set('number', 4)
        ->set('name', 'Šaltibarščiai')
        ->set('price', 3.80)
        ->set('subcategory_id', $this->visi->id)
        ->call('save')
        ->assertHasNoErrors();

    $product = OtherProduct::where('name', 'Šaltibarščiai')->sole();
    expect($product->number)->toBe(4);

    Livewire::test(OtherProductsTable::class)
        ->call('editProduct', $product->id)
        ->set('number', -1)
        ->call('save')
        ->assertHasErrors(['number'])
        ->set('number', 5)
        ->call('save')
        ->assertHasNoErrors();

    expect($product->fresh()->number)->toBe(5);
});

it('stores the position given when a product is created', function () {
    expect(OtherProduct::create(['name' => 'Naujas', 'price' => 1.0, 'position' => 5])->fresh()->position)->toBe(5);
});
