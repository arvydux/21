<?php

use App\Livewire\Buttons\AlcoholicDrinks;
use App\Livewire\KasaCategorySection;
use App\Livewire\Menu\CreateForm\AlcoholicDrinksCreateForm;
use App\Livewire\Menu\Tables\AlcoholicDrinksTable;
use App\Livewire\Orders\OrdersList;
use App\Models\AlcoholicDrink;
use App\Models\Category;
use App\Models\Drink;
use App\Models\FreeNumbers;
use App\Models\Order;
use App\Models\Subcategory;
use Livewire\Livewire;

beforeEach(function () {
    $this->category = Category::create(['name' => 'Alkoholis', 'component_name' => 'alkoholis', 'position' => 0]);
    $this->visi = Subcategory::create(['name' => 'Visi', 'category_id' => $this->category->id, 'position' => 0]);
    $this->beer = AlcoholicDrink::factory()->create(['number' => 2, 'name' => 'Švyturys', 'price' => 3.50, 'subcategory_id' => $this->visi->id]);
});

it('shows the drinks with their menu numbers on the cashier screen', function () {
    AlcoholicDrink::factory()->create(['name' => 'Paslėptas', 'show' => false, 'subcategory_id' => $this->visi->id]);

    Livewire::test(AlcoholicDrinks::class, ['subcategoryId' => $this->visi->id])
        ->assertSeeInOrder(['>2<', 'Švyturys'], escape: false)
        ->assertDontSee('Paslėptas');
});

it('adds a drink to the cart with one click', function () {
    $this->beer->update(['package' => 0.10]);

    Livewire::test(AlcoholicDrinks::class)
        ->call('addProduct', 'Švyturys', '3.50')
        ->assertDispatched('change-order');

    $order = Order::sole();
    expect(json_decode($order->name, true))->toBe([['Švyturys' => '3.50']])
        ->and((float) $order->order_price)->toBe(3.5)
        ->and((float) $order->package)->toBe(0.1)
        ->and($order->category)->toBe(AlcoholicDrink::ORDER_CATEGORY);
});

it('increments the amount when the same drink is added again', function () {
    $component = Livewire::test(AlcoholicDrinks::class);
    $component->call('addProduct', 'Švyturys', '3.50');
    $component->call('addProduct', 'Švyturys', '3.50');

    expect(Order::sole()->amount)->toBe(2);
});

it('does not merge with a soft drink of the same name and price', function () {
    Order::create(['name' => json_encode([['Švyturys' => '3.50']]), 'order_price' => 3.5, 'amount' => 1, 'category' => 3]);

    Livewire::test(AlcoholicDrinks::class)->call('addProduct', 'Švyturys', '3.50');

    expect(Order::count())->toBe(2);
});

it('does not print alcohol on the kitchen ticket', function () {
    Order::create(['name' => json_encode([['Švyturys' => 3.5]]), 'amount' => 1, 'category' => AlcoholicDrink::ORDER_CATEGORY]);

    expect(Livewire::test(OrdersList::class)->instance()->shouldPrintForKitchen())->toBeFalse();
});

it('decrements the stock when the order is made', function () {
    FreeNumbers::create(['number' => 1]);
    $this->beer->update(['left' => 5]);

    $component = Livewire::test(AlcoholicDrinks::class);
    $component->call('addProduct', 'Švyturys', '3.50');
    $component->call('addProduct', 'Švyturys', '3.50');

    ob_start();
    Livewire::test(OrdersList::class)->call('makeOrder');
    ob_end_clean();

    expect($this->beer->fresh()->left)->toBe(3)
        ->and(Order::count())->toBe(0);
});

it('creates a drink from the menu admin', function () {
    Livewire::test(AlcoholicDrinksCreateForm::class)
        ->set('number', 1)
        ->set('name', 'Vynas')
        ->set('price', 4.00)
        ->set('subcategory_id', $this->visi->id)
        ->call('save')
        ->assertHasNoErrors();

    $wine = AlcoholicDrink::where('name', 'Vynas')->sole();
    expect($wine->number)->toBe(1)
        ->and((float) $wine->price)->toBe(4.0)
        ->and(Drink::count())->toBe(0);
});

it('requires a name, price and subcategory for a new drink', function () {
    Livewire::test(AlcoholicDrinksCreateForm::class)
        ->call('save')
        ->assertHasErrors(['name', 'price', 'subcategory_id']);
});

it('only offers alcohol subcategories in the menu admin', function () {
    $drinks = Category::create(['name' => 'Gėrimai', 'component_name' => 'drinks', 'position' => 1]);
    Subcategory::create(['name' => 'Karšti gėrimai', 'category_id' => $drinks->id, 'position' => 0]);

    Livewire::test(AlcoholicDrinksCreateForm::class)
        ->assertSee('Visi')
        ->assertDontSee('Karšti gėrimai');
});

it('edits a drink from the menu admin', function () {
    Livewire::test(AlcoholicDrinksTable::class)
        ->assertSee('Švyturys')
        ->call('editProduct', $this->beer->id)
        ->set('price', 3.80)
        ->set('show', false)
        ->call('save')
        ->assertHasNoErrors();

    expect((float) $this->beer->fresh()->price)->toBe(3.8)
        ->and($this->beer->fresh()->show)->toBeFalsy();
});

it('shows the drinks when the category is opened on the cashier screen', function () {
    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'alkoholis', $this->category->id)
        ->call('selectSubcategory', $this->visi->id)
        ->assertSeeLivewire(AlcoholicDrinks::class);
});

it('adds the empty category to an existing deployment', function () {
    $this->category->delete();
    Category::create(['name' => 'Gėrimai', 'component_name' => 'drinks', 'position' => 6]);

    $migration = require database_path('migrations/2026_10_09_130805_add_alkoholis_category.php');
    $migration->up();
    $migration->up();

    $category = Category::where('component_name', 'alkoholis')->sole();

    expect($category->name)->toBe('Alkoholis')
        ->and($category->position)->toBe(7)
        ->and(Subcategory::where('category_id', $category->id)->pluck('name')->all())->toBe(['Visi']);
});

it('leaves a fresh database to the seeders', function () {
    Category::query()->delete();

    $migration = require database_path('migrations/2026_10_09_130805_add_alkoholis_category.php');
    $migration->up();

    expect(Category::count())->toBe(0);
});
