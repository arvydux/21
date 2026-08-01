<?php

use App\Livewire\MenuSubcategory;
use App\Models\Category;
use App\Models\Ceburek;
use App\Models\Subcategory;
use Livewire\Livewire;

function ceburekCategory(): Category
{
    return Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai']);
}

it('creates a subcategory scoped to the selected category', function () {
    $category = ceburekCategory();

    Livewire::test(MenuSubcategory::class)
        ->call('selectCategory', 'ceburekai', $category->id)
        ->set('newName', 'Mėsiniai')
        ->call('create')
        ->assertHasNoErrors();

    expect(Subcategory::where('category_id', $category->id)->where('name', 'Mėsiniai')->exists())->toBeTrue();
});

it('requires a name to create a subcategory', function () {
    $category = ceburekCategory();

    Livewire::test(MenuSubcategory::class)
        ->call('selectCategory', 'ceburekai', $category->id)
        ->set('newName', '')
        ->call('create')
        ->assertHasErrors(['newName' => 'required']);
});

it('renames a subcategory', function () {
    $category = ceburekCategory();
    $sub = Subcategory::create(['name' => 'Old', 'category_id' => $category->id, 'position' => 0]);

    Livewire::test(MenuSubcategory::class)
        ->call('selectCategory', 'ceburekai', $category->id)
        ->call('editSubcategory', $sub->id)
        ->set('editName', 'Naujas')
        ->call('update')
        ->assertHasNoErrors();

    expect($sub->fresh()->name)->toBe('Naujas');
});

it('moves products to the default Visi subcategory when their subcategory is deleted', function () {
    $category = ceburekCategory();
    $visi = Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0]);
    $meat = Subcategory::create(['name' => 'Mėsiniai', 'category_id' => $category->id, 'position' => 1]);
    $product = Ceburek::create(['name' => 'Su kiauliena', 'price' => 3.5, 'sign' => 'A', 'show' => true, 'category' => 1, 'position' => 0, 'subcategory_id' => $meat->id]);

    Livewire::test(MenuSubcategory::class)
        ->call('selectCategory', 'ceburekai', $category->id)
        ->call('deleteSubcategory', $meat->id);

    expect(Subcategory::find($meat->id))->toBeNull();
    expect($product->fresh()->subcategory_id)->toBe($visi->id);
});

it('reorders subcategories', function () {
    $category = ceburekCategory();
    $a = Subcategory::create(['name' => 'A', 'category_id' => $category->id, 'position' => 0]);
    $b = Subcategory::create(['name' => 'B', 'category_id' => $category->id, 'position' => 1]);

    Livewire::test(MenuSubcategory::class)
        ->call('selectCategory', 'ceburekai', $category->id)
        ->call('updateOrder', [
            ['value' => $a->id, 'order' => 1],
            ['value' => $b->id, 'order' => 0],
        ]);

    expect($a->fresh()->position)->toBe(1);
    expect($b->fresh()->position)->toBe(0);
});
