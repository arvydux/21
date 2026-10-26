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
