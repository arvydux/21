<?php

use App\Livewire\Buttons\Drinks;
use App\Livewire\Orders\OrdersList;
use App\Models\Drink;
use App\Models\Order;
use Livewire\Livewire;

it('shows each cart line with its unit price and line total', function () {
    Order::create([
        'name' => json_encode([['Pasiflorų arbata' => '4.50']]),
        'order_price' => 4.50,
        'amount' => 2,
        'category' => 3,
    ]);

    Livewire::test(OrdersList::class)
        ->assertSee('Pasiflorų arbata')
        ->assertSee('4,50 € / vnt.')
        ->assertSee('9,00 €')
        ->assertSee('2 prekės')
        ->assertSee('Vietoje');
});

it('includes toppings and the package in the unit price', function () {
    $order = Order::create([
        'name' => json_encode([['Čeburekas' => '3.00']]),
        'toppings' => json_encode([['Sūris' => '0.50']]),
        'order_price' => 3.50,
        'package' => 0.20,
        'amount' => 3,
        'category' => 1,
    ]);

    $component = Livewire::test(OrdersList::class);

    expect($component->instance()->unitPrice($order))->toEqualWithDelta(3.70, 0.001);
    $component->assertSee('11,10 €');
});

it('shows an empty cart message and a zero total without orders', function () {
    Livewire::test(OrdersList::class)
        ->assertSee('Krepšelis tuščias')
        ->assertSee('0 prekių')
        ->assertSee('0,00 €');
});

it('counts cart amounts per product name for the button badges', function () {
    Order::create(['name' => json_encode([['Kava juoda' => '2.00']]), 'order_price' => 2, 'amount' => 2, 'category' => 3]);
    Order::create(['name' => json_encode([['Kava juoda' => '2.00']]), 'order_price' => 2, 'amount' => 1, 'takeaway' => true, 'category' => 3]);

    Livewire::test(OrdersList::class)
        ->call('addOneOrder', Order::first()->id)
        ->dispatch('change-order')
        ->assertDispatched('cart-updated', counts: ['Kava juoda' => 4]);
});

it('labels the item count with the correct Lithuanian plural', function (int $count, string $label) {
    expect(Livewire::test(OrdersList::class)->instance()->itemCountLabel($count))->toBe($label);
})->with([
    [1, '1 prekė'],
    [2, '2 prekės'],
    [9, '9 prekės'],
    [10, '10 prekių'],
    [11, '11 prekių'],
    [15, '15 prekių'],
    [21, '21 prekė'],
    [22, '22 prekės'],
]);

it('renders product cards with price and a cart badge bound to the store', function () {
    Drink::create(['name' => 'Kava Latte', 'price' => 2.8, 'show' => true, 'position' => 1, 'number' => 8]);

    Livewire::test(Drinks::class, ['showAll' => true])
        ->assertSeeInOrder(['>8<', 'Kava Latte', '2,80 €'], escape: false)
        ->assertSeeHtml('$store.cart.count(\'Kava Latte\')');
});

it('does not poll for ready orders from the cart', function () {
    Livewire::test(OrdersList::class)
        ->assertDontSeeHtml('fetchReadyOrders')
        ->assertDontSeeHtml('/get-ready-orders');
});

it('splits the cart sum into food, drinks and packages', function () {
    Order::create(['name' => json_encode([['Kibinas' => '3.50']]), 'order_price' => 3.50, 'amount' => 2, 'package' => 0.20, 'category' => 2]);
    Order::create(['name' => json_encode([['Kava juoda' => '2.00']]), 'order_price' => 2.00, 'amount' => 1, 'category' => 3]);

    $component = Livewire::test(OrdersList::class);

    expect($component->instance()->categorySums())->toEqualWithDelta([
        'Kepiniai' => 7.00,
        'Gėrimai' => 2.00,
        'Pakuotės' => 0.40,
    ], 0.001);
    $component->assertSeeInOrder(['Kepiniai', '7,00 €', 'Gėrimai', '2,00 €', 'Pakuotės', '0,40 €', 'Iš viso', '9,40 €']);
});

it('hides the empty category sums', function () {
    Order::create(['name' => json_encode([['Kava juoda' => '2.00']]), 'order_price' => 2.00, 'amount' => 1, 'category' => 3]);

    expect(Livewire::test(OrdersList::class)->instance()->categorySums())->toBe(['Gėrimai' => 2.0]);
});
