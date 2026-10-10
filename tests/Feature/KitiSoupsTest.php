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

it('adds the desserts to a "Desertai" subcategory after "Sriubos", one record per ice cream topping', function () {
    (new Database\Seeders\SoupSubcategorySeeder)->run();

    $migration = require database_path('migrations/2026_10_10_194926_add_dessert_subcategory_to_kiti.php');
    $migration->up();
    $migration->up();

    $desserts = Subcategory::where('category_id', $this->category->id)->where('name', 'Desertai')->sole();

    expect($desserts->position)->toBe(2)
        ->and(OtherProduct::where('subcategory_id', $desserts->id)->orderBy('number')->get()->map(fn (OtherProduct $product) => [$product->number, $product->name, (float) $product->price])->all())
        ->toBe([
            [1, 'Žagarėliai su ledais ir padažu', 5.5],
            [2, 'Ledai su vyšnių užpilu', 3.9],
            [3, 'Ledai su šokolado užpilu', 3.9],
            [4, 'Ledai su karamelės užpilu', 3.9],
        ]);
});

it('adds the kids menu to a "Vaikų meniu" subcategory', function () {
    $migration = require database_path('migrations/2026_10_10_195123_add_kids_menu_subcategory_to_kiti.php');
    $migration->up();
    $migration->up();

    $kidsMenu = Subcategory::where('category_id', $this->category->id)->where('name', 'Vaikų meniu')->sole();

    expect(OtherProduct::where('subcategory_id', $kidsMenu->id)->orderBy('number')->get()->map(fn (OtherProduct $product) => [$product->number, $product->name, (float) $product->price])->all())
        ->toBe([
            [1, 'Gruzdintos bulvytės „FRI“ su pomidorų padažu', 3.5],
            [2, 'Bulvių ir vištienos rutuliukai (be padažo)', 4.9],
            [3, 'Vištienos filė juostelės su bulvytėmis, agurkais ir pomidorų padažu', 5.9],
            [4, 'Pieniškos dešrelės su bulvytėmis, agurkais ir pomidorų padažu', 5.5],
        ]);
});

it('moves the original potato and chicken balls into the kids menu instead of duplicating them', function () {
    $balls = OtherProduct::create(['name' => 'Bulvių ir vištienos rutuliukai', 'price' => 4.9, 'show' => true, 'position' => 2, 'subcategory_id' => $this->visi->id]);

    (require database_path('migrations/2026_10_10_195123_add_kids_menu_subcategory_to_kiti.php'))->up();

    $balls->refresh();
    expect($balls->name)->toBe('Bulvių ir vištienos rutuliukai (be padažo)')
        ->and($balls->number)->toBe(2)
        ->and($balls->subcategory->name)->toBe('Vaikų meniu')
        ->and(OtherProduct::where('name', 'like', 'Bulvių ir vištienos rutuliukai%')->count())->toBe(1)
        ->and(OtherProduct::count())->toBe(4);
});
