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

it('includes toppings and the package in the unit price of a takeaway line', function () {
    $order = Order::create([
        'name' => json_encode([['Čeburekas' => '3.00']]),
        'toppings' => json_encode([['Sūris' => '0.50']]),
        'order_price' => 3.50,
        'package' => 0.20,
        'amount' => 3,
        'takeaway' => true,
        'category' => 1,
    ]);

    $component = Livewire::test(OrdersList::class);

    expect($component->instance()->unitPrice($order))->toEqualWithDelta(3.70, 0.001);
    $component->assertSee('11,10 €');
});

it('does not charge or show the package for a line eaten on the spot', function () {
    $order = Order::create([
        'name' => json_encode([['Čeburekas' => '3.00']]),
        'order_price' => 3.00,
        'package' => 0.20,
        'amount' => 2,
        'category' => 1,
    ]);

    $component = Livewire::test(OrdersList::class);

    expect($component->instance()->unitPrice($order))->toEqualWithDelta(3.00, 0.001)
        ->and($component->instance()->categorySums())->not->toHaveKey('Pakuotės');
    $component->assertDontSee('Pakuotė')->assertSee('6,00 €');
});

it('adds and removes the package when the line is switched between takeaway and on the spot', function () {
    $order = Order::create([
        'name' => json_encode([['Čeburekas' => '3.00']]),
        'order_price' => 3.00,
        'package' => 0.20,
        'amount' => 1,
        'category' => 1,
    ]);

    $component = Livewire::test(OrdersList::class)
        ->call('changeTakeaway', $order->id)
        ->dispatch('change-order')
        ->assertSee('+ Pakuotė')
        ->assertSee('3,20 €');

    expect($component->instance()->categorySums()['Pakuotės'])->toEqualWithDelta(0.20, 0.001);

    $component->call('changeTakeaway', $order->id)
        ->dispatch('change-order')
        ->assertDontSee('Pakuotė')
        ->assertSee('3,00 €');
});

it('adds new products on the spot and keeps takeaway lines of the same drink separate', function () {
    $drink = Drink::create(['name' => 'Kava juoda', 'price' => 2.00, 'package' => 0.10, 'show' => true, 'position' => 1]);

    Livewire::test(Drinks::class)->call('addProduct', $drink->name, '2.00');
    $first = Order::sole();
    expect($first->takeaway)->toBeFalsy();

    $first->update(['takeaway' => true]);
    Livewire::test(Drinks::class)->call('addProduct', $drink->name, '2.00');
    Livewire::test(Drinks::class)->call('addProduct', $drink->name, '2.00');

    expect(Order::count())->toBe(2)
        ->and($first->fresh()->amount)->toBe(1)
        ->and(Order::where('takeaway', false)->sole()->amount)->toBe(2);
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
    Order::create(['name' => json_encode([['Kibinas' => '3.50']]), 'order_price' => 3.50, 'amount' => 2, 'package' => 0.20, 'takeaway' => true, 'category' => 2]);
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

it('shows the whole order in a summary popup without action buttons', function () {
    Order::create([
        'name' => json_encode([['Kibinas su kopūstais' => '2.00']]),
        'order_price' => 2.00,
        'amount' => 5,
        'category' => 1,
    ]);
    Order::create([
        'name' => json_encode([['Kibinas su kopūstais' => '2.00']]),
        'order_price' => 2.00,
        'package' => 0.20,
        'amount' => 1,
        'category' => 1,
        'takeaway' => true,
        'comments' => 'Be svogūnų',
    ]);

    $html = Livewire::test(OrdersList::class)
        ->assertSeeHtml('data-order-summary-trigger')
        ->assertSeeHtml('x-on:keydown.escape.window="summaryOpen = false"')
        ->assertSee('Visas užsakymas')
        ->html();

    $summary = str(preg_split('/data-order-summary(?!-)/', $html)[1])->before('Iš viso')->toString();

    expect(substr_count($summary, 'wire:key="summary-'))->toBe(2)
        ->and($summary)->toContain('5×')
        ->and($summary)->toContain('10,00 €')
        ->and($summary)->toContain('Be svogūnų')
        ->and($summary)->toContain('Išsinešimui')
        ->and($summary)->not->toContain('wire:click');
});

it('lets the cashier drag the cart and product lists with a finger', function () {
    Livewire::test(OrdersList::class)
        ->assertSeeHtml('x-drag-scroll x-scroll-fade data-drag-scroll class="no-scrollbar flex min-h-0 flex-1 touch-pan-y select-none');

    Livewire::test(\App\Livewire\KasaCategorySection::class)
        ->call('showProductMenu', 'drinks', null)
        ->assertSeeHtml('x-drag-scroll x-scroll-fade data-drag-scroll class="no-scrollbar @container');

    $this->get(route('kasa'))->assertSee("Alpine.directive('drag-scroll'", false)
        ->assertSee("Alpine.directive('scroll-fade'", false);
});

it('marks alcohol lines as 18+ and warns about the age check', function () {
    Order::create([
        'name' => json_encode([['Vynas „KAGOR“ (150 ml)' => '5.20']]),
        'order_price' => 5.20,
        'amount' => 1,
        'category' => \App\Models\AlcoholicDrink::ORDER_CATEGORY,
    ]);
    Order::create([
        'name' => json_encode([['Kava Espresso' => '2.20']]),
        'order_price' => 2.20,
        'amount' => 1,
        'category' => 3,
    ]);

    $html = Livewire::test(OrdersList::class)
        ->assertSeeHtml('data-adults-warning')
        ->assertSee('Patikrinkite, ar pirkėjui yra 18 metų.')
        ->html();

    expect(substr_count($html, '>18+</span>'))->toBe(3)
        ->and(substr_count($html, 'ring-red-400/60'))->toBe(1);
});

it('does not warn about the age check without alcohol', function () {
    Order::create([
        'name' => json_encode([['Kava Espresso' => '2.20']]),
        'order_price' => 2.20,
        'amount' => 1,
        'category' => 3,
    ]);

    Livewire::test(OrdersList::class)
        ->assertDontSeeHtml('data-adults-warning')
        ->assertDontSeeHtml('>18+</span>');
});

it('edits a cart line comment in the cashier styled popup', function () {
    $order = Order::create([
        'name' => json_encode([['Kibinas su kopūstais' => '2.00']]),
        'order_price' => 2.00,
        'amount' => 1,
        'category' => 2,
        'comments' => 'Šiltas',
    ]);

    Livewire::test(OrdersList::class)
        ->assertSeeHtml('data-comment-modal')
        ->assertSeeHtml("wire:submit.prevent=\"makeCommentForOrder({$order->id})\"")
        ->assertSee('Komentarai bei pageidavimai')
        ->call('openCommentForOrder', $order->id)
        ->assertSet('comments', 'Šiltas')
        ->set('comments', 'Be svogūnų')
        ->call('makeCommentForOrder', $order->id);

    expect($order->fresh()->comments)->toBe('Be svogūnų');
});

it('lets fields and popups inside drag-scroll lists keep their own pointer handling', function () {
    $this->get(route('kasa'))->assertSee("e.target.closest('dialog, input, textarea, select')", false);
});
