<?php

use App\Livewire\Orders\OrdersList;
use App\Models\Order;
use Livewire\Livewire;

function makeKitchenTestOrder(int $category): Order
{
    return Order::create([
        'name' => json_encode([['Produktas' => 1.0]]),
        'amount' => 1,
        'category' => $category,
    ]);
}

function shouldPrintForKitchen(): bool
{
    return Livewire::test(OrdersList::class)->instance()->shouldPrintForKitchen();
}

it('prints to kitchen when order contains Ceburekai (category 1)', function () {
    makeKitchenTestOrder(1);

    expect(shouldPrintForKitchen())->toBeTrue();
});

it('prints to kitchen when order contains Kiti (category 4)', function () {
    makeKitchenTestOrder(4);

    expect(shouldPrintForKitchen())->toBeTrue();
});

it('prints to kitchen for a mixed order containing Ceburekai and drinks', function () {
    makeKitchenTestOrder(3);
    makeKitchenTestOrder(1);

    expect(shouldPrintForKitchen())->toBeTrue();
});

it('does not print to kitchen for a drinks-only order (category 3)', function () {
    makeKitchenTestOrder(3);

    expect(shouldPrintForKitchen())->toBeFalse();
});

it('does not print to kitchen for a kepiniai-only order (category 2)', function () {
    makeKitchenTestOrder(2);

    expect(shouldPrintForKitchen())->toBeFalse();
});

it('does not print to kitchen when the cart is empty', function () {
    expect(shouldPrintForKitchen())->toBeFalse();
});
