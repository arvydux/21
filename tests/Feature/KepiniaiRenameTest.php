<?php

use App\Livewire\Buttons\Kepiniai;
use App\Livewire\KasaCategorySection;
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
