<?php

use App\Livewire\Orders\OrdersList;
use App\Models\FreeNumbers;
use App\Models\Order;
use App\Models\OrderNumbers;
use Livewire\Livewire;

beforeEach(function () {
    FreeNumbers::create(['number' => 7]);
});

function addOrderInCategory(int $category): void
{
    Order::create([
        'name' => json_encode([['Produktas' => 1.0]]),
        'amount' => 1,
        'category' => $category,
    ]);
}

function takeOrderNumber(): ?int
{
    return Livewire::test(OrdersList::class)->instance()->takeOrderNumber();
}

it('gives no order number when nothing goes to the kitchen', function (array $categories) {
    foreach ($categories as $category) {
        addOrderInCategory($category);
    }

    expect(takeOrderNumber())->toBeNull()
        ->and(OrderNumbers::count())->toBe(0)
        ->and(FreeNumbers::first()->number)->toBe(7);
})->with([
    'Kepiniai' => [[2]],
    'Gėrimai' => [[3]],
    'Alkoholis' => [[7]],
    'Kepiniai, Gėrimai and Alkoholis' => [[2, 3, 7]],
]);

it('takes the next order number when something goes to the kitchen', function (array $categories) {
    foreach ($categories as $category) {
        addOrderInCategory($category);
    }

    expect(takeOrderNumber())->toBe(7)
        ->and(OrderNumbers::where('number', 7)->exists())->toBeTrue()
        ->and(FreeNumbers::first()->number)->toBe(8);
})->with([
    'Čeburekai' => [[1]],
    'Kiti' => [[4]],
    'Čeburekai with Gėrimai' => [[1, 3]],
]);

it('remembers the taken order for the link in the receipt QR code', function () {
    addOrderInCategory(1);
    $component = Livewire::test(OrdersList::class)->instance();

    $number = $component->takeOrderNumber();
    $taken = (fn () => $this->takenOrder)->call($component);

    expect($taken->is(OrderNumbers::sole()))->toBeTrue()
        ->and($taken->number)->toBe($number)
        ->and($taken->statusUrl())->toBe('https://status.prusakovuuzeiga.lt/online/'.$taken->token);
});

it('forgets the previous order when the next checkout gets no number', function () {
    addOrderInCategory(1);
    $component = Livewire::test(OrdersList::class)->instance();
    $component->takeOrderNumber();
    Order::truncate();
    addOrderInCategory(3);

    expect($component->takeOrderNumber())->toBeNull()
        ->and((fn () => $this->takenOrder)->call($component))->toBeNull();
});
