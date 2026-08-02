<?php

use App\Livewire\KasaCategorySection;
use App\Models\Category;
use App\Models\Ceburek;
use App\Models\Subcategory;
use Livewire\Livewire;

function makeCeburekCategoryWithSubcategories(): array
{
    $category = Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai']);

    $meat = Subcategory::create(['name' => 'Mėsiniai', 'category_id' => $category->id, 'position' => 0]);
    $cheese = Subcategory::create(['name' => 'Sūriniai', 'category_id' => $category->id, 'position' => 1]);

    Ceburek::create(['name' => 'Su kiauliena', 'price' => 3.5, 'sign' => 'A', 'show' => true, 'category' => 1, 'position' => 0, 'subcategory_id' => $meat->id]);
    Ceburek::create(['name' => '2-jų rūšių sūris', 'price' => 3.0, 'sign' => 'B', 'show' => true, 'category' => 1, 'position' => 0, 'subcategory_id' => $cheese->id]);

    return compact('category', 'meat', 'cheese');
}

it('shows subcategory buttons after a category is clicked', function () {
    ['category' => $category] = makeCeburekCategoryWithSubcategories();

    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'ceburekai', $category->id)
        ->assertSet('selectedSubcategoryId', null)
        ->assertSee('Mėsiniai')
        ->assertSee('Sūriniai');
});

it('shows only products of the selected subcategory', function () {
    ['category' => $category, 'meat' => $meat] = makeCeburekCategoryWithSubcategories();

    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'ceburekai', $category->id)
        ->call('selectSubcategory', $meat->id)
        ->assertSet('selectedSubcategoryId', $meat->id)
        ->assertSee('Su kiauliena')
        ->assertDontSee('2-jų rūšių sūris');
});

it('returns to the subcategory list via backToSubcategories', function () {
    ['category' => $category, 'meat' => $meat] = makeCeburekCategoryWithSubcategories();

    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'ceburekai', $category->id)
        ->call('selectSubcategory', $meat->id)
        ->call('backToSubcategories')
        ->assertSet('selectedSubcategoryId', null)
        ->assertSee('Mėsiniai')
        ->assertSee('Sūriniai');
});

it('resets the selected subcategory when switching category', function () {
    ['category' => $category, 'meat' => $meat] = makeCeburekCategoryWithSubcategories();
    $other = Category::create(['name' => 'Kiti', 'component_name' => 'other']);

    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'ceburekai', $category->id)
        ->call('selectSubcategory', $meat->id)
        ->call('showProductMenu', 'other', $other->id)
        ->assertSet('selectedSubcategoryId', null);
});

it('hides subcategory buttons and shows the back button in drilldown mode', function () {
    cache()->forever('subcategory_layout_mode', 'drilldown');
    ['category' => $category, 'meat' => $meat] = makeCeburekCategoryWithSubcategories();

    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'ceburekai', $category->id)
        ->call('selectSubcategory', $meat->id)
        ->assertSee('Atgal')
        ->assertDontSee('Sūriniai');
});

it('keeps subcategory buttons and hides the back button in persistent mode', function () {
    cache()->forever('subcategory_layout_mode', 'persistent');
    ['category' => $category, 'meat' => $meat] = makeCeburekCategoryWithSubcategories();

    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'ceburekai', $category->id)
        ->call('selectSubcategory', $meat->id)
        ->assertDontSee('Atgal')
        ->assertSee('Mėsiniai')
        ->assertSee('Sūriniai')
        ->assertSee('Su kiauliena');
});

it('defaults to drilldown mode when no preference is stored', function () {
    cache()->forget('subcategory_layout_mode');
    ['category' => $category, 'meat' => $meat] = makeCeburekCategoryWithSubcategories();

    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'ceburekai', $category->id)
        ->call('selectSubcategory', $meat->id)
        ->assertSee('Atgal');
});
