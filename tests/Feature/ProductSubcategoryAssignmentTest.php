<?php

use App\Livewire\Menu\CreateForm\CebureksCreateForm;
use App\Livewire\Menu\Tables\CebureksTable;
use App\Models\Category;
use App\Models\Ceburek;
use App\Models\Subcategory;
use Livewire\Livewire;

function ceburekCategoryWithSubcategory(): array
{
    $category = Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai']);
    $sub = Subcategory::create(['name' => 'Mėsiniai', 'category_id' => $category->id, 'position' => 0]);

    return [$category, $sub];
}

it('creates a product assigned to the chosen subcategory', function () {
    [, $sub] = ceburekCategoryWithSubcategory();

    Livewire::test(CebureksCreateForm::class)
        ->set('name', 'Naujas čeburekas')
        ->set('price', 4.20)
        ->set('subcategory_id', $sub->id)
        ->call('save')
        ->assertHasNoErrors();

    $product = Ceburek::where('name', 'Naujas čeburekas')->first();
    expect($product)->not->toBeNull();
    expect($product->subcategory_id)->toBe($sub->id);
});

it('requires a subcategory when creating a product', function () {
    ceburekCategoryWithSubcategory();

    Livewire::test(CebureksCreateForm::class)
        ->set('name', 'Be subkategorijos')
        ->set('price', 4.20)
        ->set('subcategory_id', null)
        ->call('save')
        ->assertHasErrors(['subcategory_id']);

    expect(Ceburek::where('name', 'Be subkategorijos')->exists())->toBeFalse();
});

it('moves an existing product to a different subcategory when edited', function () {
    [$category, $meat] = ceburekCategoryWithSubcategory();
    $cheese = Subcategory::create(['name' => 'Sūriniai', 'category_id' => $category->id, 'position' => 1]);
    $product = Ceburek::create(['name' => 'Su kiauliena', 'price' => 3.5, 'sign' => 'A', 'show' => true, 'category' => 1, 'position' => 0, 'subcategory_id' => $meat->id]);

    Livewire::test(CebureksTable::class)
        ->call('editProduct', $product->id)
        ->set('subcategory_id', $cheese->id)
        ->call('save')
        ->assertHasNoErrors();

    expect($product->fresh()->subcategory_id)->toBe($cheese->id);
});
