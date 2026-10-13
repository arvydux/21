<?php

use App\Livewire\Menu\CreateForm\CebureksCreateForm;
use App\Livewire\Menu\CreateForm\PotatoDishesCreateForm;
use App\Livewire\Menu\Tables;
use App\Models;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

/**
 * @param  class-string<Model>  $model
 */
function menuProduct(string $model, string $name, float $price = 2.0): Model
{
    $product = new $model;
    $columns = Schema::getColumnListing($product->getTable());

    $product->forceFill(array_filter([
        'name' => $name,
        'price' => $price,
        'half_price' => in_array('half_price', $columns) ? $price / 2 : null,
        'sign' => in_array('sign', $columns) ? 'A' : null,
        'show' => true,
        'position' => 0,
    ], fn ($value) => $value !== null))->save();

    return $product;
}

it('saves a package price typed with a comma when editing a product', function () {
    $category = Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai']);
    $product = menuProduct(Models\Ceburek::class, 'Sūris + grybai', 5.5);
    $product->update(['subcategory_id' => $category->subcategories()->first()->id]);

    Livewire::test(Tables\CebureksTable::class)
        ->call('editProduct', $product->id)
        ->set('price', '5,90')
        ->set('package', '0,50')
        ->call('save')
        ->assertHasNoErrors();

    expect((float) $product->fresh()->price)->toBe(5.9)
        ->and((float) $product->fresh()->package)->toBe(0.5);
});

it('rejects a negative or non numeric price', function (string $price) {
    $category = Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai']);
    $product = menuProduct(Models\Ceburek::class, 'Kiauliena');
    $product->update(['subcategory_id' => $category->subcategories()->first()->id]);

    Livewire::test(Tables\CebureksTable::class)
        ->call('editProduct', $product->id)
        ->set('price', $price)
        ->call('save')
        ->assertHasErrors('price');
})->with(['negative' => '-1', 'text' => 'abc']);

it('creates products with decimal prices typed with a comma', function () {
    $category = Category::create(['name' => 'Bulviniai patiekalai', 'component_name' => 'bulviniai']);
    $subcategory = Subcategory::where('category_id', $category->id)->first();

    Livewire::test(PotatoDishesCreateForm::class)
        ->set('name', 'Blynai')
        ->set('price', '7,60')
        ->set('half_price', '4,20')
        ->set('subcategory_id', $subcategory->id)
        ->call('save')
        ->assertHasNoErrors();

    $dish = Models\PotatoDish::where('name', 'Blynai')->sole();
    expect((float) $dish->price)->toBe(7.6)->and((float) $dish->half_price)->toBe(4.2);

    $ceburekCategory = Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai']);
    Livewire::test(CebureksCreateForm::class)
        ->set('name', 'Naujas')
        ->set('price', '5,55')
        ->set('subcategory_id', $ceburekCategory->subcategories()->first()->id)
        ->call('save')
        ->assertHasNoErrors();

    expect((float) Models\Ceburek::where('name', 'Naujas')->value('price'))->toBe(5.55);
});

it('sets the package price of every product in a category at once', function (string $table, string $model) {
    $first = menuProduct($model, 'Pirmas');
    $second = menuProduct($model, 'Antras');

    Livewire::test($table)
        ->assertSeeHtml('data-apply-to-all')
        ->set('bulkField', 'package')
        ->set('bulkValue', '0,30')
        ->call('applyToAll')
        ->assertHasNoErrors()
        ->assertSet('bulkValue', null);

    expect((float) $first->fresh()->package)->toBe(0.3)
        ->and((float) $second->fresh()->package)->toBe(0.3);
})->with([
    'čeburekai' => [Tables\CebureksTable::class, Models\Ceburek::class],
    'kepiniai' => [Tables\KepiniaiTable::class, Models\Kepiniai::class],
    'gėrimai' => [Tables\DrinksTable::class, Models\Drink::class],
    'kiti' => [Tables\OtherProductsTable::class, Models\OtherProduct::class],
    'bulviniai' => [Tables\PotatoDishesTable::class, Models\PotatoDish::class],
    'koldūnai' => [Tables\KoldunaiDishesTable::class, Models\KoldunaiDish::class],
    'alkoholis' => [Tables\AlcoholicDrinksTable::class, Models\AlcoholicDrink::class],
    'priedai' => [Tables\ToppingsTable::class, Models\Topping::class],
    'bulvinių priedai' => [Tables\PotatoToppingsTable::class, Models\PotatoTopping::class],
    'koldūnų priedai' => [Tables\KoldunaiToppingsTable::class, Models\KoldunaiTopping::class],
]);

it('sets the price of every product at once and leaves other categories alone', function () {
    $drink = menuProduct(Models\Drink::class, 'Kava', 2.2);
    $ceburek = menuProduct(Models\Ceburek::class, 'Čeburekas', 5.5);

    Livewire::test(Tables\DrinksTable::class)
        ->set('bulkField', 'price')
        ->set('bulkValue', '2.50')
        ->call('applyToAll');

    expect((float) $drink->fresh()->price)->toBe(2.5)
        ->and((float) $ceburek->fresh()->price)->toBe(5.5);
});

it('only lets the bulk update change price columns and refuses bad amounts', function () {
    menuProduct(Models\Drink::class, 'Kava');

    Livewire::test(Tables\DrinksTable::class)
        ->set('bulkField', 'name')
        ->set('bulkValue', '1')
        ->call('applyToAll')
        ->assertHasErrors('bulkField')
        ->set('bulkField', 'package')
        ->set('bulkValue', '-0,50')
        ->call('applyToAll')
        ->assertHasErrors('bulkValue')
        ->set('bulkValue', '')
        ->call('applyToAll')
        ->assertHasErrors('bulkValue');

    expect(Models\Drink::where('name', 'Kava')->value('package'))->toBeNull();
});

it('offers the half portion price in the potato dishes bulk update', function () {
    Livewire::test(Tables\PotatoDishesTable::class)
        ->assertSee('Pusės porcijos kaina')
        ->assertSee('Pakuotės kaina');
});
