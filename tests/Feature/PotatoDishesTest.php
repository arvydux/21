<?php

use App\Livewire\Buttons\PotatoDishes;
use App\Livewire\Menu\CreateForm\PotatoDishesCreateForm;
use App\Livewire\Menu\Tables\PotatoToppingsTable;
use App\Livewire\Orders\OrdersList;
use App\Models\Category;
use App\Models\FreeNumbers;
use App\Models\Order;
use App\Models\PotatoDish;
use App\Models\PotatoTopping;
use App\Models\Subcategory;
use Livewire\Livewire;

beforeEach(function () {
    $this->dish = PotatoDish::factory()->create([
        'name' => 'Žemaičių blynai su kiauliena',
        'half_price' => 4.00,
        'price' => 7.40,
    ]);
    $this->sourCream = PotatoTopping::factory()->selectedByDefault()->create(['name' => 'Grietinė', 'price' => 0.00, 'position' => 0]);
    $this->cracklings = PotatoTopping::factory()->create(['name' => 'Spirgučiai ir grietinė', 'price' => 0.40, 'position' => 1]);
    $this->mushroomSauce = PotatoTopping::factory()->create(['name' => 'Pievagrybių padažas', 'price' => 0.60, 'position' => 2]);
});

function onlyOrderLine(): Order
{
    expect(Order::count())->toBe(1);

    return Order::first();
}

it('shows potato dishes on the cashier screen', function () {
    Livewire::test(PotatoDishes::class, ['showAll' => true])
        ->assertSee('Žemaičių blynai su kiauliena');
});

it('pre-selects a full portion with the default sauce when a dish is chosen', function () {
    Livewire::test(PotatoDishes::class)
        ->set('isHalfPortion', true)
        ->call('chooseProduct', $this->dish->id)
        ->assertSet('isHalfPortion', false)
        ->assertSet('toppings', ['Grietinė']);
});

it('adds a full portion with the default sauce', function () {
    Livewire::test(PotatoDishes::class)
        ->call('chooseProduct', $this->dish->id)
        ->call('addOrder')
        ->assertDispatched('change-order');

    $order = onlyOrderLine();
    expect(json_decode($order->name, true))->toBe([['Žemaičių blynai su kiauliena (pilna)' => 7.4]])
        ->and(json_decode($order->toppings, true))->toBe([['Grietinė' => 0]])
        ->and((float) $order->order_price)->toBe(7.4)
        ->and($order->category)->toBe(PotatoDish::ORDER_CATEGORY);
});

it('adds a half portion at the half price', function () {
    Livewire::test(PotatoDishes::class)
        ->call('chooseProduct', $this->dish->id)
        ->set('isHalfPortion', true)
        ->call('addOrder');

    $order = onlyOrderLine();
    expect(json_decode($order->name, true))->toBe([['Žemaičių blynai su kiauliena ½' => 4]])
        ->and((float) $order->order_price)->toBe(4.0);
});

it('adds a dish without any sauce when the default one is switched off', function () {
    Livewire::test(PotatoDishes::class)
        ->call('chooseProduct', $this->dish->id)
        ->set('toppings', [])
        ->call('addOrder');

    expect(json_decode(onlyOrderLine()->toppings, true))->toBe([]);
});

it('adds the price of every selected sauce', function () {
    Livewire::test(PotatoDishes::class)
        ->call('chooseProduct', $this->dish->id)
        ->set('toppings', ['Grietinė', 'Spirgučiai ir grietinė', 'Pievagrybių padažas'])
        ->call('addOrder');

    expect((float) onlyOrderLine()->order_price)->toBe(8.4);
});

it('ignores sauces that do not exist', function () {
    Livewire::test(PotatoDishes::class)
        ->call('chooseProduct', $this->dish->id)
        ->set('toppings', ['Nežinomas padažas'])
        ->call('addOrder');

    expect(json_decode(onlyOrderLine()->toppings, true))->toBe([])
        ->and((float) onlyOrderLine()->order_price)->toBe(7.4);
});

it('resets to a full portion with the default sauce after adding', function () {
    Livewire::test(PotatoDishes::class)
        ->call('chooseProduct', $this->dish->id)
        ->set('isHalfPortion', true)
        ->set('toppings', ['Pievagrybių padažas'])
        ->call('addOrder')
        ->assertSet('isHalfPortion', false)
        ->assertSet('toppings', ['Grietinė']);
});

it('increments the amount when the same dish, portion and sauces are added again', function () {
    $component = Livewire::test(PotatoDishes::class)->call('chooseProduct', $this->dish->id);
    $component->call('addOrder');
    $component->call('addOrder');

    expect(onlyOrderLine()->amount)->toBe(2);
});

it('keeps half and full portions as separate cart lines', function () {
    Livewire::test(PotatoDishes::class)
        ->call('chooseProduct', $this->dish->id)
        ->call('addOrder')
        ->set('isHalfPortion', true)
        ->call('addOrder');

    expect(Order::count())->toBe(2);
});

it('keeps lines with different sauces separate', function () {
    Livewire::test(PotatoDishes::class)
        ->call('chooseProduct', $this->dish->id)
        ->call('addOrder')
        ->set('toppings', [])
        ->call('addOrder');

    expect(Order::count())->toBe(2);
});

it('does nothing when no dish was chosen', function () {
    Livewire::test(PotatoDishes::class)->call('addOrder');

    expect(Order::count())->toBe(0);
});

it('prints potato dishes on the kitchen ticket', function () {
    Order::create(['name' => json_encode([['Blynai (pilna)' => 7.4]]), 'amount' => 1, 'category' => PotatoDish::ORDER_CATEGORY]);

    expect(Livewire::test(OrdersList::class)->instance()->shouldPrintForKitchen())->toBeTrue();
});

it('recovers the product name from an order label', function (string $label) {
    expect(PotatoDish::nameFromOrderLabel($label))->toBe('Žemaičių blynai su kiauliena');
})->with([
    'half portion' => 'Žemaičių blynai su kiauliena ½',
    'full portion' => 'Žemaičių blynai su kiauliena (pilna)',
    'no suffix' => 'Žemaičių blynai su kiauliena',
]);

it('decrements the stock of potato dishes and sauces when the order is made', function () {
    FreeNumbers::create(['number' => 1]);
    $this->dish->update(['left' => 5]);
    $this->sourCream->update(['left' => 10]);

    $component = Livewire::test(PotatoDishes::class)->call('chooseProduct', $this->dish->id);
    $component->call('addOrder');
    $component->set('isHalfPortion', true)->call('addOrder');

    ob_start();
    Livewire::test(OrdersList::class)->call('makeOrder');
    ob_end_clean();

    expect($this->dish->fresh()->left)->toBe(3)
        ->and($this->sourCream->fresh()->left)->toBe(8)
        ->and(Order::count())->toBe(0);
});

it('creates a potato dish with half and full portion prices from the menu admin', function () {
    $category = Category::create(['name' => 'Bulviniai patiekalai', 'component_name' => 'bulviniai']);
    $subcategory = Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0]);

    Livewire::test(PotatoDishesCreateForm::class)
        ->set('name', 'Bulvinukai su sūriu')
        ->set('half_price', 4.20)
        ->set('price', 7.60)
        ->set('subcategory_id', $subcategory->id)
        ->call('save')
        ->assertHasNoErrors();

    $dish = PotatoDish::where('name', 'Bulvinukai su sūriu')->first();
    expect((float) $dish->half_price)->toBe(4.2)
        ->and((float) $dish->price)->toBe(7.6);
});

it('requires the half portion price', function () {
    $category = Category::create(['name' => 'Bulviniai patiekalai', 'component_name' => 'bulviniai']);
    $subcategory = Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0]);

    Livewire::test(PotatoDishesCreateForm::class)
        ->set('name', 'Be pusės kainos')
        ->set('price', 7.60)
        ->set('subcategory_id', $subcategory->id)
        ->call('save')
        ->assertHasErrors(['half_price']);
});

it('lets the admin choose which sauces are pre-selected', function () {
    Livewire::test(PotatoToppingsTable::class)
        ->call('editProduct', $this->mushroomSauce->id)
        ->set('is_default', true)
        ->call('save')
        ->assertHasNoErrors();

    expect($this->mushroomSauce->fresh()->is_default)->toBeTrue();
});

it('adds the category, dishes and sauces to an existing deployment', function () {
    PotatoDish::query()->delete();
    PotatoTopping::query()->delete();
    Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai', 'position' => 3]);

    $migration = require database_path('migrations/2026_10_08_141847_add_bulviniai_patiekalai_category.php');
    $migration->up();
    $migration->up();

    $category = Category::where('component_name', 'bulviniai')->sole();
    $visi = Subcategory::where('category_id', $category->id)->where('name', 'Visi')->sole();

    expect($category->name)->toBe('Bulviniai patiekalai')
        ->and($category->position)->toBe(4)
        ->and(PotatoDish::count())->toBe(6)
        ->and(PotatoDish::whereNot('subcategory_id', $visi->id)->exists())->toBeFalse()
        ->and(PotatoTopping::count())->toBe(3)
        ->and(PotatoTopping::where('is_default', true)->pluck('name')->all())->toBe(['Grietinė']);
});

it('leaves a fresh database to the seeders', function () {
    $migration = require database_path('migrations/2026_10_08_141847_add_bulviniai_patiekalai_category.php');
    $migration->up();

    expect(Category::count())->toBe(0);
});

it('shows the potato dishes when the category is opened on the cashier screen', function () {
    $category = Category::create(['name' => 'Bulviniai patiekalai', 'component_name' => 'bulviniai']);
    $visi = Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0]);
    $this->dish->update(['subcategory_id' => $visi->id]);

    Livewire::test(\App\Livewire\KasaCategorySection::class)
        ->call('showProductMenu', 'bulviniai', $category->id)
        ->call('selectSubcategory', $visi->id)
        ->assertSeeLivewire(PotatoDishes::class);
});

it('lists potato dishes with both prices in the menu admin', function () {
    Livewire::test(\App\Livewire\Menu\Tables\PotatoDishesTable::class)
        ->assertSee('Žemaičių blynai su kiauliena')
        ->assertSee('4.00 €')
        ->assertSee('7.40 €');
});

it('shows the line total including paid sauces in the cart', function () {
    Order::create([
        'name' => json_encode([['Žemaičių blynai su kiauliena (pilna)' => 7.4]]),
        'toppings' => json_encode([['Spirgučiai ir grietinė' => 0.4]]),
        'order_price' => 7.80,
        'amount' => 2,
        'category' => PotatoDish::ORDER_CATEGORY,
    ]);

    Livewire::test(OrdersList::class)
        ->assertSeeHtml('&times; 2 = 15.6 €')
        ->assertDontSeeHtml('&times; 2 = 14.8 €');
});

it('shows the menu number on the cashier button', function () {
    $this->dish->update(['number' => 7]);

    Livewire::test(PotatoDishes::class, ['showAll' => true])
        ->assertSeeInOrder(['>7<', 'Žemaičių blynai su kiauliena'], escape: false);
});

it('saves the menu number from the menu admin', function () {
    $category = Category::create(['name' => 'Bulviniai patiekalai', 'component_name' => 'bulviniai']);
    $this->dish->update(['subcategory_id' => Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0])->id]);

    Livewire::test(\App\Livewire\Menu\Tables\PotatoDishesTable::class)
        ->call('editProduct', $this->dish->id)
        ->set('number', 3)
        ->call('save')
        ->assertHasNoErrors();

    expect($this->dish->fresh()->number)->toBe(3);
});

it('rejects a menu number that is not a positive whole number', function (mixed $number) {
    Livewire::test(\App\Livewire\Menu\Tables\PotatoDishesTable::class)
        ->call('editProduct', $this->dish->id)
        ->set('number', $number)
        ->call('save')
        ->assertHasErrors(['number']);
})->with([
    'zero' => 0,
    'negative' => -1,
]);

it('creates a dish with a menu number, or without one', function (mixed $number, ?int $expected) {
    $category = Category::create(['name' => 'Bulviniai patiekalai', 'component_name' => 'bulviniai']);
    $subcategory = Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0]);

    Livewire::test(PotatoDishesCreateForm::class)
        ->set('number', $number)
        ->set('name', 'Naujas patiekalas')
        ->set('half_price', 3.00)
        ->set('price', 5.00)
        ->set('subcategory_id', $subcategory->id)
        ->call('save')
        ->assertHasNoErrors();

    expect(PotatoDish::where('name', 'Naujas patiekalas')->value('number'))->toBe($expected);
})->with([
    'with number' => [8, 8],
    'empty' => ['', null],
]);

it('numbers the existing dishes like the paper menu', function () {
    PotatoDish::query()->delete();
    (new \Database\Seeders\PotatoDishSeeder)->run();
    PotatoDish::query()->update(['number' => null]);

    $migration = require database_path('migrations/2026_10_08_145855_add_number_to_potato_dishes_table.php');
    \Illuminate\Support\Facades\Schema::table('potato_dishes', fn ($table) => $table->dropColumn('number'));
    $migration->up();

    expect(PotatoDish::orderBy('number')->pluck('number', 'name')->all())->toBe([
        'Žemaičių blynai su kiauliena' => 1,
        'Žemaičių blynai su vištiena' => 2,
        'Žemaičių blynai su pievagrybiais' => 3,
        'Bulvinukai su sūriu' => 4,
        'Bulviniai blynai su mėsa „Kėdainių“' => 5,
        'Traškūs bulviniai blynai be mėsos' => 6,
    ]);
});

it('moves the three Žemaičių blynai dishes into their own subcategory', function () {
    PotatoDish::query()->delete();
    $category = Category::create(['name' => 'Bulviniai patiekalai', 'component_name' => 'bulviniai', 'position' => 4]);
    $visi = Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0]);
    (new \Database\Seeders\PotatoDishSeeder)->run();
    PotatoDish::query()->update(['subcategory_id' => $visi->id]);

    $migration = require database_path('migrations/2026_10_10_201254_add_zemaiciu_blynai_subcategory.php');
    $migration->up();
    $migration->up();

    $blynai = Subcategory::where('category_id', $category->id)->where('name', 'Žemaičių blynai')->sole();

    expect($blynai->position)->toBe(1)
        ->and(PotatoDish::where('subcategory_id', $blynai->id)->orderBy('number')->pluck('number', 'name')->all())->toBe([
            'Žemaičių blynai su kiauliena' => 1,
            'Žemaičių blynai su vištiena' => 2,
            'Žemaičių blynai su pievagrybiais' => 3,
        ])
        ->and(PotatoDish::where('subcategory_id', $visi->id)->orderBy('number')->pluck('name')->all())->toBe([
            'Bulvinukai su sūriu',
            'Bulviniai blynai su mėsa „Kėdainių“',
            'Traškūs bulviniai blynai be mėsos',
        ])
        ->and($visi->fresh())->not->toBeNull()
        ->and(PotatoDish::count())->toBe(6);
});

it('moves the last three dishes into a "Bulviniai blynai" subcategory, renumbers them and removes the empty "Visi"', function () {
    PotatoDish::query()->delete();
    $category = Category::create(['name' => 'Bulviniai patiekalai', 'component_name' => 'bulviniai', 'position' => 4]);
    $visi = Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0]);
    (new \Database\Seeders\PotatoDishSeeder)->run();
    PotatoDish::query()->update(['subcategory_id' => $visi->id]);
    (new \Database\Seeders\ZemaiciuBlynaiSubcategorySeeder)->run();

    $migration = require database_path('migrations/2026_10_10_201510_add_bulviniai_blynai_subcategory.php');
    $migration->up();
    $migration->up();

    $pancakes = Subcategory::where('category_id', $category->id)->where('name', 'Bulviniai blynai')->sole();

    expect($pancakes->position)->toBe(2)
        ->and(PotatoDish::where('subcategory_id', $pancakes->id)->orderBy('number')->pluck('number', 'name')->all())->toBe([
            'Bulvinukai su sūriu' => 1,
            'Bulviniai blynai su mėsa „Kėdainių“' => 2,
            'Traškūs bulviniai blynai be mėsos' => 3,
        ])
        ->and($visi->fresh())->toBeNull()
        ->and(Subcategory::where('category_id', $category->id)->orderBy('position')->pluck('name')->all())->toBe(['Žemaičių blynai', 'Bulviniai blynai'])
        ->and(PotatoDish::count())->toBe(6);
});

it('opens the potato dishes category without a "Visi" subcategory in every cashier layout', function (string $layoutMode) {
    cache()->put('subcategory_layout_mode', $layoutMode);
    $category = Category::create(['name' => 'Bulviniai patiekalai', 'component_name' => 'bulviniai', 'position' => 4]);
    $blynai = Subcategory::create(['name' => 'Žemaičių blynai', 'category_id' => $category->id, 'position' => 1]);
    $this->dish->update(['subcategory_id' => $blynai->id]);

    Livewire::test(\App\Livewire\KasaCategorySection::class)
        ->call('showProductMenu', 'bulviniai', $category->id)
        ->assertSet('selectedSubcategoryId', null)
        ->assertSee('Žemaičių blynai')
        ->call('selectSubcategory', $blynai->id)
        ->assertSeeLivewire(PotatoDishes::class);
})->with(['drilldown', 'persistent', 'visi_default']);
