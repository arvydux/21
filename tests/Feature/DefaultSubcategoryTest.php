<?php

use App\Livewire\KasaCategorySection;
use App\Livewire\MenuSubcategory;
use App\Models\Category;
use App\Models\Subcategory;
use Database\Seeders\DefaultSubcategorySeeder;
use Livewire\Livewire;

function defaultSubcategory(Category $category): Subcategory
{
    return Subcategory::where('category_id', $category->id)->where('name', 'Visi')->firstOrFail();
}

it('gives a new category the default Visi subcategory', function () {
    $category = Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai']);

    expect(defaultSubcategory($category)->position)->toBe(0);
});

it('restores a missing Visi as the first tab and keeps the other tabs in order', function () {
    $category = Category::create(['name' => 'Kiti', 'component_name' => 'other']);
    defaultSubcategory($category)->delete();
    Subcategory::create(['name' => 'Sriubos', 'category_id' => $category->id, 'position' => 0]);
    Subcategory::create(['name' => 'Desertai', 'category_id' => $category->id, 'position' => 1]);

    (new DefaultSubcategorySeeder)->run();
    (new DefaultSubcategorySeeder)->run();

    expect(Subcategory::where('category_id', $category->id)->orderBy('position')->pluck('name')->all())
        ->toBe(['Visi', 'Sriubos', 'Desertai']);
});

it('opens a category on Visi with all of its products', function () {
    cache()->forever('subcategory_layout_mode', 'visi_default');
    $category = Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai']);
    Subcategory::create(['name' => 'Su sūriu', 'category_id' => $category->id, 'position' => 1]);

    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'ceburekai', $category->id)
        ->assertSet('selectedSubcategoryId', defaultSubcategory($category)->id)
        ->assertViewHas('showAll', true);
});

it('does not let the admin delete, rename or move Visi', function () {
    $category = Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai']);
    $visi = defaultSubcategory($category);
    $other = Subcategory::create(['name' => 'Su sūriu', 'category_id' => $category->id, 'position' => 1]);

    Livewire::test(MenuSubcategory::class)
        ->call('selectCategory', 'ceburekai', $category->id)
        ->assertSeeHtml('data-default-subcategory')
        ->assertDontSeeHtml("deleteSubcategory({$visi->id})")
        ->call('deleteSubcategory', $visi->id)
        ->call('editSubcategory', $visi->id)
        ->assertSet('editingId', 0)
        ->call('updateOrder', [['value' => $other->id, 'order' => 1], ['value' => $visi->id, 'order' => 2]]);

    expect($visi->fresh())->not->toBeNull()
        ->and($visi->fresh()->name)->toBe('Visi')
        ->and($visi->fresh()->position)->toBe(0);
});

it('does not let the admin create or rename another subcategory to Visi', function () {
    $category = Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai']);
    $other = Subcategory::create(['name' => 'Su sūriu', 'category_id' => $category->id, 'position' => 1]);

    Livewire::test(MenuSubcategory::class)
        ->call('selectCategory', 'ceburekai', $category->id)
        ->set('newName', 'Visi')
        ->call('create')
        ->assertHasErrors(['newName' => 'not_in'])
        ->call('editSubcategory', $other->id)
        ->set('editName', 'Visi')
        ->call('update')
        ->assertHasErrors(['editName' => 'not_in']);

    expect(Subcategory::where('category_id', $category->id)->where('name', 'Visi')->count())->toBe(1);
});
