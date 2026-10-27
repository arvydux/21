<?php

use App\Livewire\Orders\OrdersList;
use App\Models\FreeNumbers;
use App\Models\Order;
use App\Models\OrderNumbers;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;

beforeEach(function () {
    FreeNumbers::create(['number' => 7]);
});

function addOrderLine(bool $forKitchen): void
{
    Order::create([
        'name' => json_encode([['Produktas' => 1.0]]),
        'amount' => 1,
        'for_kitchen' => $forKitchen,
    ]);
}

function takeOrderNumber(): ?int
{
    return Livewire::test(OrdersList::class)->instance()->takeOrderNumber();
}

it('gives no order number when nothing goes to the kitchen', function (array $lines) {
    foreach ($lines as $forKitchen) {
        addOrderLine($forKitchen);
    }

    expect(takeOrderNumber())->toBeNull()
        ->and(OrderNumbers::count())->toBe(0)
        ->and(FreeNumbers::first()->number)->toBe(7);
})->with([
    'one line not for the kitchen' => [[false]],
    'several lines not for the kitchen' => [[false, false, false]],
]);

it('takes the next order number when something goes to the kitchen', function (array $lines) {
    foreach ($lines as $forKitchen) {
        addOrderLine($forKitchen);
    }

    expect(takeOrderNumber())->toBe(7)
        ->and(OrderNumbers::where('number', 7)->exists())->toBeTrue()
        ->and(FreeNumbers::first()->number)->toBe(8);
})->with([
    'one kitchen line' => [[true]],
    'a kitchen line with a line not for the kitchen' => [[false, true]],
]);

it('remembers the taken order for the link in the receipt QR code', function () {
    addOrderLine(true);
    $component = Livewire::test(OrdersList::class)->instance();

    $number = $component->takeOrderNumber();
    $taken = (fn () => $this->takenOrder)->call($component);

    expect($taken->is(OrderNumbers::sole()))->toBeTrue()
        ->and($taken->number)->toBe($number)
        ->and($taken->statusUrl())->toBe('https://status.prusakovuuzeiga.lt/online/'.$taken->token);
});

it('forgets the previous order when the next checkout gets no number', function () {
    addOrderLine(true);
    $component = Livewire::test(OrdersList::class)->instance();
    $component->takeOrderNumber();
    Order::truncate();
    addOrderLine(false);

    expect($component->takeOrderNumber())->toBeNull()
        ->and((fn () => $this->takenOrder)->call($component))->toBeNull();
});

it('finishes the checkout and logs a warning when a printer is unreachable', function () {
    Log::spy();
    Order::create([
        'name' => json_encode([['Produktas' => 1.0]]),
        'toppings' => json_encode([]),
        'amount' => 1,
        'for_kitchen' => true,
    ]);

    Livewire::test(OrdersList::class)
        ->call('makeOrder')
        ->assertDispatched('order-made')
        ->assertNoRedirect();

    expect(OrderNumbers::where('number', 7)->exists())->toBeTrue()
        ->and(Order::count())->toBe(0);

    Log::shouldHaveReceived('warning')->withArgs(fn (string $message) => str_starts_with($message, "Couldn't print to printer"));
});

it('keeps the kitchen lines of the order for the kitchen screen', function () {
    Order::create([
        'name' => json_encode([['Kibinas su mėsa' => 3.5]]),
        'toppings' => json_encode([['Grietinė' => 0.5], ['Česnakų padažas' => 0.5]]),
        'amount' => 2,
        'category' => 1,
        'takeaway' => true,
        'comments' => 'Be svogūnų',
        'for_kitchen' => true,
    ]);
    Order::create(['name' => json_encode([['Kava' => 2.0]]), 'amount' => 1, 'for_kitchen' => false]);

    takeOrderNumber();

    $item = OrderNumbers::sole()->kitchenItems()->sole();

    expect($item->name)->toBe('Kibinas su mėsa')
        ->and($item->amount)->toBe(2)
        ->and($item->toppings)->toBe(['Grietinė', 'Česnakų padažas'])
        ->and($item->category)->toBe(1)
        ->and($item->takeaway)->toBeTrue()
        ->and($item->comments)->toBe('Be svogūnų');
});

it('drops the kitchen lines with the order number when numbers start over', function () {
    $old = OrderNumbers::create(['number' => 3, 'is_taken' => true]);
    $old->kitchenItems()->create(['name' => 'Kibinas', 'amount' => 1]);
    FreeNumbers::first()->update(['number' => 1000]);
    addOrderLine(true);

    takeOrderNumber();

    expect(OrderNumbers::find($old->id))->toBeNull()
        ->and(\App\Models\KitchenOrderItem::where('order_number_id', $old->id)->exists())->toBeFalse();
});

it('still gives the order number when the kitchen items cannot be saved', function () {
    Log::spy();
    \Illuminate\Support\Facades\Schema::drop('kitchen_order_items');
    addOrderLine(true);

    expect(takeOrderNumber())->toBe(7)
        ->and(OrderNumbers::where('number', 7)->exists())->toBeTrue();

    Log::shouldHaveReceived('warning')->withArgs(fn (string $message) => str_contains($message, "Couldn't save the kitchen items of order 7"));
});
