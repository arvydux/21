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
    cache()->forever('subcategory_layout_mode', 'drilldown');
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
    cache()->forever('subcategory_layout_mode', 'drilldown');
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

it('opens every category on Visi when no preference is stored, e.g. on a new computer', function () {
    cache()->forget('subcategory_layout_mode');
    $category = Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai']);

    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'ceburekai', $category->id)
        ->assertSet('selectedSubcategoryId', Subcategory::where('category_id', $category->id)->where('name', 'Visi')->value('id'))
        ->assertViewHas('showAll', true);
});

function makeCeburekCategoryWithVisi(): array
{
    $category = Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai']);

    $visi = Subcategory::firstOrCreate(['name' => 'Visi', 'category_id' => $category->id], ['position' => 0]);
    $meat = Subcategory::create(['name' => 'Mėsiniai', 'category_id' => $category->id, 'position' => 1]);

    Ceburek::create(['name' => 'Produktas Visi', 'price' => 3.5, 'sign' => 'A', 'show' => true, 'category' => 1, 'position' => 0, 'subcategory_id' => $visi->id]);
    Ceburek::create(['name' => 'Produktas Mesa', 'price' => 3.0, 'sign' => 'B', 'show' => true, 'category' => 1, 'position' => 1, 'subcategory_id' => $meat->id]);

    return compact('category', 'visi', 'meat');
}

it('auto-selects Visi and shows all category products in visi_default mode', function () {
    cache()->forever('subcategory_layout_mode', 'visi_default');
    ['category' => $category, 'visi' => $visi] = makeCeburekCategoryWithVisi();

    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'ceburekai', $category->id)
        ->assertSet('selectedSubcategoryId', $visi->id)
        ->assertDontSee('Atgal')
        ->assertSee('Mėsiniai')          // subcategory buttons stay visible
        ->assertSee('Produktas Visi')
        ->assertSee('Produktas Mesa');   // product from a different subcategory is shown too
});

it('filters to the clicked subcategory in visi_default mode', function () {
    cache()->forever('subcategory_layout_mode', 'visi_default');
    ['category' => $category, 'meat' => $meat] = makeCeburekCategoryWithVisi();

    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'ceburekai', $category->id)
        ->call('selectSubcategory', $meat->id)
        ->assertSee('Produktas Mesa')
        ->assertDontSee('Produktas Visi');
});

it('does not auto-select Visi in drilldown or persistent modes', function () {
    cache()->forever('subcategory_layout_mode', 'persistent');
    ['category' => $category] = makeCeburekCategoryWithVisi();

    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'ceburekai', $category->id)
        ->assertSet('selectedSubcategoryId', null);
});

it('labels the subcategory tray with the selected category name', function () {
    $category = Category::create(['name' => 'Gėrimai', 'component_name' => 'drinks']);
    Subcategory::create(['name' => 'Karšti gėrimai', 'category_id' => $category->id, 'position' => 0]);

    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'drinks', $category->id)
        ->assertSee('Gėrimai')
        ->assertSee('Karšti gėrimai');
});
