<?php

use App\Livewire\Buttons\Ceburekai;
use App\Livewire\Buttons\Drinks;
use App\Livewire\Buttons\Kibinai;
use App\Livewire\Buttons\OtherProducts;
use App\Models\Category;
use App\Models\Ceburek;
use App\Models\Drink;
use App\Models\Kibinai as KibinaiModel;
use App\Models\OtherProduct;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

function ceburekSubcategory(): Subcategory
{
    $category = Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai', 'position' => 0]);

    return Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0]);
}

it('renders the buttons component without error after A cleanup', function () {
    $sub = ceburekSubcategory();
    Ceburek::create(['name' => 'Su kiauliena', 'price' => 3.5, 'sign' => 'A', 'show' => true, 'category' => 1, 'position' => 0, 'subcategory_id' => $sub->id]);

    Livewire::test(Ceburekai::class, ['subcategoryId' => $sub->id])
        ->assertOk()
        ->assertSee('Su kiauliena')
        ->call('refreshProducts') // second round-trip: catches broken hydration from removed props
        ->assertOk();
});

it('uses the default column count when no setting is stored', function () {
    cache()->forget('pos_button_columns');
    $sub = ceburekSubcategory();

    Livewire::test(Ceburekai::class, ['subcategoryId' => $sub->id])
        ->assertSee('md:grid-cols-4', false);
});

it('respects the stored column setting via Cache::many', function () {
    cache()->forever('pos_button_columns', 5);
    $sub = ceburekSubcategory();

    Livewire::test(Ceburekai::class, ['subcategoryId' => $sub->id])
        ->assertSee('md:grid-cols-5', false)
        ->assertDontSee('md:grid-cols-4', false);
});

it('reads all four button settings in a single Cache::many call', function () {
    $sub = ceburekSubcategory();

    Cache::spy()->shouldReceive('many')->andReturn([
        'pos_button_style' => 'compact',
        'pos_button_columns' => 4,
        'pos_button_gap' => 4,
        'pos_button_padding' => 3,
    ]);

    Livewire::test(Ceburekai::class, ['subcategoryId' => $sub->id])->assertOk();

    Cache::shouldHaveReceived('many')->once();
});

it('renders every button component without error', function () {
    $category = Category::create(['name' => 'Gėrimai', 'component_name' => 'drinks', 'position' => 0]);
    $sub = Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0]);

    Drink::create(['name' => 'Kava', 'price' => 2.0, 'show' => true, 'position' => 0, 'subcategory_id' => $sub->id]);
    KibinaiModel::create(['name' => 'Kibinas', 'price' => 3.0, 'show' => true, 'position' => 0, 'subcategory_id' => $sub->id]);
    OtherProduct::create(['name' => 'Kita', 'price' => 1.0, 'show' => true, 'position' => 0, 'subcategory_id' => $sub->id]);

    Livewire::test(Drinks::class, ['subcategoryId' => $sub->id])->assertOk();
    Livewire::test(Kibinai::class, ['subcategoryId' => $sub->id])->assertOk();
    Livewire::test(OtherProducts::class, ['subcategoryId' => $sub->id])->assertOk();
});
