<?php

use App\Livewire\Orders\OrdersList;
use App\Models\Order;
use Livewire\Livewire;

function firstDarkRow(GdImage $image): ?int
{
    for ($y = 0; $y < imagesy($image); $y++) {
        for ($x = 0; $x < imagesx($image); $x++) {
            if ((imagecolorat($image, $x, $y) & 0xFF) < 128) {
                return $y;
            }
        }
    }

    return null;
}

it('leaves about 2 cm of blank paper above the kitchen receipt', function () {
    Order::create([
        'name' => json_encode([['Čeburekas su kiauliena' => 5.5]]),
        'amount' => 2,
        'category' => 1,
        'for_kitchen' => true,
    ]);

    $image = Livewire::test(OrdersList::class)->instance()->kitchenReceipt(42, false)->toImage();

    expect(firstDarkRow($image))->toBeGreaterThanOrEqual(OrdersList::KITCHEN_TOP_MARGIN);
});

it('prints kitchen order lines larger than before', function () {
    expect(OrdersList::KITCHEN_ITEM_SIZE)->toBeGreaterThan(24);
});
