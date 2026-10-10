<?php

use App\Livewire\Buttons\Kepiniai;
use App\Livewire\KasaCategorySection;
use App\Livewire\Menu\CreateForm\KepiniaiCreateForm;
use App\Livewire\Menu\Tables\KepiniaiTable;
use App\Models\Category;
use App\Models\Kepiniai as KepiniaiModel;
use App\Models\Subcategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

it('renames the Kibinai table and category on an existing deployment', function () {
    $migration = require database_path('migrations/2026_10_10_181814_rename_kibinai_to_kepiniai.php');
    $migration->down();
    $category = Category::create(['name' => 'Kibinai', 'component_name' => 'kibinai', 'position' => 1]);
    DB::table('kibinais')->insert(['name' => 'Kibinas su kiauliena', 'price' => 2.0, 'show' => true]);

    $migration->up();

    expect(Schema::hasTable('kibinais'))->toBeFalse()
        ->and(Schema::hasTable('kepiniai'))->toBeTrue()
        ->and(KepiniaiModel::pluck('name')->all())->toBe(['Kibinas su kiauliena'])
        ->and($category->fresh()->only('name', 'component_name'))->toBe(['name' => 'Kepiniai', 'component_name' => 'kepiniai']);
});

it('shows the Kepiniai products when the category is opened on the cashier screen', function () {
    $category = Category::create(['name' => 'Kepiniai', 'component_name' => 'kepiniai', 'position' => 1]);
    $visi = Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0]);
    KepiniaiModel::create(['name' => 'Kibinas su kiauliena', 'price' => 2.0, 'show' => true, 'position' => 0, 'subcategory_id' => $visi->id]);

    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'kepiniai', $category->id)
        ->call('selectSubcategory', $visi->id)
        ->assertSeeLivewire(Kepiniai::class);

    Livewire::test(Kepiniai::class, ['subcategoryId' => $visi->id])
        ->assertSee('Kibinas su kiauliena')
        ->call('addProduct', 'Kibinas su kiauliena', '2.00');

    expect(App\Models\Order::sole()->category)->toBe(2);
});

it('lists Kepiniai in the menu admin', function () {
    KepiniaiModel::create(['name' => 'Kibinas su vištiena', 'price' => 2.2, 'show' => true, 'position' => 0]);

    Livewire::test(KepiniaiTable::class)->assertSee('Kibinas su vištiena');
});

it('adds the kibinai to a "Kibinai" subcategory of "Kepiniai"', function () {
    $category = Category::create(['name' => 'Kepiniai', 'component_name' => 'kepiniai', 'position' => 1]);
    $visi = Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0]);
    $donut = KepiniaiModel::create(['name' => 'Spurga', 'price' => 1.8, 'show' => true, 'position' => 0, 'subcategory_id' => $visi->id]);
    $pork = KepiniaiModel::create(['name' => 'Kibinas su kiauliena', 'price' => 2.0, 'show' => true, 'position' => 1, 'subcategory_id' => $visi->id]);
    $chicken = KepiniaiModel::create(['name' => 'Kibinas su vistiena', 'price' => 2.2, 'show' => true, 'position' => 2, 'subcategory_id' => $visi->id]);

    $migration = require database_path('migrations/2026_10_10_183930_add_kibinai_subcategory_to_kepiniai.php');
    $migration->up();
    $migration->up();

    $kibinai = Subcategory::where('category_id', $category->id)->where('name', 'Kibinai')->sole();

    expect($kibinai->position)->toBe(1)
        ->and(KepiniaiModel::where('subcategory_id', $kibinai->id)->orderBy('position')->get()->map(fn (KepiniaiModel $product) => [$product->number, $product->name, (float) $product->price])->all())
        ->toBe([
            [1, 'Kibinas su kiauliena', 2.2],
            [2, 'Kibinas su kopūstais', 2.0],
            [3, 'Kibinas su varške ir persikais', 2.2],
            [4, 'Kibinas su vištiena', 2.4],
            [5, 'Kibinas su kiauliena ir sūriu', 2.5],
        ])
        ->and($pork->fresh()->subcategory_id)->toBe($kibinai->id)
        ->and($chicken->fresh()->name)->toBe('Kibinas su vištiena')
        ->and($donut->fresh()->subcategory_id)->toBe($visi->id)
        ->and(KepiniaiModel::count())->toBe(6);
});

it('adds the donut to a "Spurgos" subcategory, renaming the original one', function () {
    $category = Category::create(['name' => 'Kepiniai', 'component_name' => 'kepiniai', 'position' => 1]);
    $visi = Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0]);
    $donut = KepiniaiModel::create(['name' => 'Spurga', 'price' => 1.8, 'show' => true, 'position' => 0, 'subcategory_id' => $visi->id]);

    $migration = require database_path('migrations/2026_10_10_184009_add_donut_subcategory_to_kepiniai.php');
    $migration->up();
    $migration->up();

    $donut->refresh();
    expect($donut->name)->toBe('Varškės spurga')
        ->and($donut->number)->toBe(1)
        ->and((float) $donut->price)->toBe(1.2)
        ->and($donut->subcategory->name)->toBe('Spurgos')
        ->and(KepiniaiModel::count())->toBe(1);
});

it('creates the donut when there is none yet', function () {
    $category = Category::create(['name' => 'Kepiniai', 'component_name' => 'kepiniai', 'position' => 1]);

    $migration = require database_path('migrations/2026_10_10_184009_add_donut_subcategory_to_kepiniai.php');
    $migration->up();

    expect(KepiniaiModel::sole()->only('name', 'subcategory_id'))
        ->toBe(['name' => 'Varškės spurga', 'subcategory_id' => Subcategory::where('category_id', $category->id)->where('name', 'Spurgos')->value('id')]);
});

it('shows the menu number on the Kepiniai button', function () {
    KepiniaiModel::create(['number' => 3, 'name' => 'Kibinas su varške ir persikais', 'price' => 2.2, 'show' => true, 'position' => 0]);

    Livewire::test(Kepiniai::class, ['showAll' => true])
        ->assertSeeInOrder(['>3<', 'Kibinas su varške ir persikais'], escape: false);
});

it('creates and edits the menu number from the menu admin', function () {
    $category = Category::create(['name' => 'Kepiniai', 'component_name' => 'kepiniai', 'position' => 1]);
    $visi = Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0]);

    Livewire::test(KepiniaiCreateForm::class)
        ->set('number', 6)
        ->set('name', 'Naujas kibinas')
        ->set('price', 2.30)
        ->set('subcategory_id', $visi->id)
        ->call('save')
        ->assertHasNoErrors();

    $product = KepiniaiModel::where('name', 'Naujas kibinas')->sole();
    expect($product->number)->toBe(6);

    Livewire::test(KepiniaiTable::class)
        ->call('editProduct', $product->id)
        ->set('number', 0)
        ->call('save')
        ->assertHasErrors(['number'])
        ->set('number', 2)
        ->call('save')
        ->assertHasNoErrors();

    expect($product->fresh()->number)->toBe(2);
});
