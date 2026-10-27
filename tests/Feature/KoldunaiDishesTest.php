<?php

use App\Livewire\Buttons\KoldunaiDishes;
use App\Livewire\KasaCategorySection;
use App\Livewire\Menu\CreateForm\KoldunaiDishesCreateForm;
use App\Livewire\Menu\CreateForm\KoldunaiToppingsCreateForm;
use App\Livewire\Menu\Tables\KoldunaiDishesTable;
use App\Livewire\Orders\OrdersList;
use App\Models\Category;
use App\Models\FreeNumbers;
use App\Models\KoldunaiDish;
use App\Models\KoldunaiTopping;
use App\Models\Order;
use App\Models\Subcategory;
use Livewire\Livewire;

beforeEach(function () {
    $this->dish = KoldunaiDish::factory()->create([
        'number' => 3,
        'name' => 'Užkepti gruzdinti koldūnai su padažu ir sūriu',
        'price' => 7.50,
    ]);
    $this->jalapenos = KoldunaiTopping::factory()->create(['name' => 'Jelapenai', 'price' => 0.60, 'position' => 0]);
    $this->onions = KoldunaiTopping::factory()->create(['name' => 'Kepinti svogūnų dribsniai', 'price' => 0.60, 'position' => 1]);
});

function onlyKoldunaiOrderLine(): Order
{
    expect(Order::count())->toBe(1);

    return Order::first();
}

it('shows the dishes with their menu numbers on the cashier screen', function () {
    Livewire::test(KoldunaiDishes::class, ['showAll' => true])
        ->assertSeeInOrder(['>3<', 'Užkepti gruzdinti koldūnai su padažu ir sūriu'], escape: false)
        ->assertSee('Jelapenai')
        ->assertSee('Kepinti svogūnų dribsniai');
});

it('starts with no toppings selected', function () {
    Livewire::test(KoldunaiDishes::class)
        ->call('chooseProduct', $this->dish->id)
        ->assertSet('toppings', []);
});

it('adds a dish without toppings at its price', function () {
    Livewire::test(KoldunaiDishes::class)
        ->call('chooseProduct', $this->dish->id)
        ->call('addOrder')
        ->assertDispatched('change-order');

    $order = onlyKoldunaiOrderLine();
    expect(json_decode($order->name, true))->toBe([['Užkepti gruzdinti koldūnai su padažu ir sūriu' => 7.5]])
        ->and(json_decode($order->toppings, true))->toBe([])
        ->and((float) $order->order_price)->toBe(7.5)
        ->and($order->category)->toBe(KoldunaiDish::ORDER_CATEGORY);
});

it('adds the price of every selected topping', function () {
    Livewire::test(KoldunaiDishes::class)
        ->call('chooseProduct', $this->dish->id)
        ->set('toppings', ['Jelapenai', 'Kepinti svogūnų dribsniai'])
        ->call('addOrder');

    $order = onlyKoldunaiOrderLine();
    expect((float) $order->order_price)->toBe(8.7)
        ->and(json_decode($order->toppings, true))->toBe([['Jelapenai' => 0.6], ['Kepinti svogūnų dribsniai' => 0.6]]);
});

it('ignores toppings that do not exist', function () {
    Livewire::test(KoldunaiDishes::class)
        ->call('chooseProduct', $this->dish->id)
        ->set('toppings', ['Grybai'])
        ->call('addOrder');

    expect(json_decode(onlyKoldunaiOrderLine()->toppings, true))->toBe([])
        ->and((float) onlyKoldunaiOrderLine()->order_price)->toBe(7.5);
});

it('clears the toppings after adding', function () {
    Livewire::test(KoldunaiDishes::class)
        ->call('chooseProduct', $this->dish->id)
        ->set('toppings', ['Jelapenai'])
        ->call('addOrder')
        ->assertSet('toppings', []);
});

it('increments the amount when the same dish and toppings are added again', function () {
    $component = Livewire::test(KoldunaiDishes::class)->call('chooseProduct', $this->dish->id);
    $component->call('addOrder');
    $component->call('addOrder');

    expect(onlyKoldunaiOrderLine()->amount)->toBe(2);
});

it('keeps lines with different toppings or takeaway choice separate', function () {
    Livewire::test(KoldunaiDishes::class)
        ->call('chooseProduct', $this->dish->id)
        ->call('addOrder')
        ->set('toppings', ['Jelapenai'])
        ->call('addOrder')
        ->set('takeaway', true)
        ->call('addOrder');

    expect(Order::count())->toBe(3);
});

it('stores the package price for the cart line', function () {
    $this->dish->update(['package' => 0.30]);

    Livewire::test(KoldunaiDishes::class)
        ->call('chooseProduct', $this->dish->id)
        ->call('addOrder');

    expect((float) onlyKoldunaiOrderLine()->package)->toBe(0.3);
});

it('does nothing when no dish was chosen', function () {
    Livewire::test(KoldunaiDishes::class)->call('addOrder');

    expect(Order::count())->toBe(0);
});

it('prints the dishes on the kitchen ticket', function () {
    Livewire::test(KoldunaiDishes::class)
        ->call('chooseProduct', $this->dish->id)
        ->call('addOrder');

    expect(Livewire::test(OrdersList::class)->instance()->shouldPrintForKitchen())->toBeTrue();
});

it('decrements the stock of dishes and toppings when the order is made', function () {
    FreeNumbers::create(['number' => 1]);
    $this->dish->update(['left' => 5]);
    $this->jalapenos->update(['left' => 10]);

    $component = Livewire::test(KoldunaiDishes::class)->call('chooseProduct', $this->dish->id);
    $component->set('toppings', ['Jelapenai'])->call('addOrder');
    $component->set('toppings', ['Jelapenai'])->call('addOrder');

    ob_start();
    Livewire::test(OrdersList::class)->call('makeOrder');
    ob_end_clean();

    expect($this->dish->fresh()->left)->toBe(3)
        ->and($this->jalapenos->fresh()->left)->toBe(8)
        ->and(Order::count())->toBe(0);
});

it('creates a dish from the menu admin', function () {
    $category = Category::create(['name' => 'Koldūnai bei bulvytės', 'component_name' => 'koldunai']);
    $subcategory = Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0]);

    Livewire::test(KoldunaiDishesCreateForm::class)
        ->set('number', 5)
        ->set('name', 'Naujas patiekalas')
        ->set('price', 6.00)
        ->set('subcategory_id', $subcategory->id)
        ->call('save')
        ->assertHasNoErrors();

    $dish = KoldunaiDish::where('name', 'Naujas patiekalas')->sole();
    expect($dish->number)->toBe(5)
        ->and((float) $dish->price)->toBe(6.0)
        ->and($dish->subcategory_id)->toBe($subcategory->id);
});

it('requires a name, price and subcategory for a new dish', function () {
    Livewire::test(KoldunaiDishesCreateForm::class)
        ->call('save')
        ->assertHasErrors(['name', 'price', 'subcategory_id']);
});

it('edits a dish from the menu admin', function () {
    $category = Category::create(['name' => 'Koldūnai bei bulvytės', 'component_name' => 'koldunai']);
    $subcategory = Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0]);
    $this->dish->update(['subcategory_id' => $subcategory->id]);

    Livewire::test(KoldunaiDishesTable::class)
        ->assertSee('7.50 €')
        ->call('editProduct', $this->dish->id)
        ->set('price', 7.90)
        ->set('package', 0.30)
        ->call('save')
        ->assertHasNoErrors();

    expect((float) $this->dish->fresh()->price)->toBe(7.9)
        ->and((float) $this->dish->fresh()->package)->toBe(0.3);
});

it('creates a topping from the menu admin', function () {
    Livewire::test(KoldunaiToppingsCreateForm::class)
        ->set('name', 'Česnakinis padažas')
        ->set('price', 0.50)
        ->call('save')
        ->assertHasNoErrors();

    expect((float) KoldunaiTopping::where('name', 'Česnakinis padažas')->value('price'))->toBe(0.5);
});

it('shows the dishes when the category is opened on the cashier screen', function () {
    $category = Category::create(['name' => 'Koldūnai bei bulvytės', 'component_name' => 'koldunai']);
    $visi = Subcategory::create(['name' => 'Visi', 'category_id' => $category->id, 'position' => 0]);
    $this->dish->update(['subcategory_id' => $visi->id]);

    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'koldunai', $category->id)
        ->call('selectSubcategory', $visi->id)
        ->assertSeeLivewire(KoldunaiDishes::class);
});

it('adds the category, dishes and toppings to an existing deployment', function () {
    KoldunaiDish::query()->delete();
    KoldunaiTopping::query()->delete();
    Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai', 'position' => 4]);

    $migration = require database_path('migrations/2026_10_08_212328_add_koldunai_category.php');
    $migration->up();
    $migration->up();

    $category = Category::where('component_name', 'koldunai')->sole();
    $visi = Subcategory::where('category_id', $category->id)->where('name', 'Visi')->sole();

    expect($category->name)->toBe('Koldūnai bei bulvytės')
        ->and($category->position)->toBe(5)
        ->and(KoldunaiDish::orderBy('number')->get()->map(fn (KoldunaiDish $dish) => [$dish->number, $dish->name, (float) $dish->price])->all())->toBe([
            [1, 'Užkeptos bulvytės „FRI“ su pikantišku padažu ir sūriu', 6.5],
            [2, 'Užkeptos bulvytės „FRI“ su pikantišku padažu, kepta šonine ir sūriu', 7.5],
            [3, 'Užkepti gruzdinti koldūnai su padažu ir sūriu', 7.5],
            [4, 'Užkepti gruzdinti koldūnai su kepta šonine, padažu ir sūriu', 8.5],
        ])
        ->and(KoldunaiDish::whereNot('subcategory_id', $visi->id)->exists())->toBeFalse()
        ->and(KoldunaiTopping::orderBy('position')->pluck('price', 'name')->map(fn ($price) => (float) $price)->all())->toBe([
            'Jelapenai' => 0.6,
            'Kepinti svogūnų dribsniai' => 0.6,
        ]);
});

it('leaves a fresh database to the seeders', function () {
    $migration = require database_path('migrations/2026_10_08_212328_add_koldunai_category.php');
    $migration->up();

    expect(Category::count())->toBe(0);
});
