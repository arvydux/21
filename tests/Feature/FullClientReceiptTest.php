<?php

use App\Livewire\Orders\OrdersList;
use App\Models\Order;
use Livewire\Livewire;

function fullReceiptLines(): array
{
    return Livewire::test(OrdersList::class)->instance()->fullReceiptLines();
}

it('lists every item of the order with amount, price and toppings', function () {
    Order::create([
        'name' => json_encode([['Čeburekas su kiauliena' => 5.5]]),
        'toppings' => json_encode([['Sūris' => 0.5]]),
        'amount' => 2,
        'category' => 1,
    ]);
    Order::create([
        'name' => json_encode([['Pepsi' => 1.8]]),
        'amount' => 1,
        'category' => 3,
    ]);

    $lines = fullReceiptLines();

    expect($lines)->toContain(str_pad('2 x Ceburekas su kiauliena', 43).'11.00')
        ->toContain(str_pad('   + Suris', 44).'1.00')
        ->toContain(str_pad('1 x Pepsi', 44).'1.80')
        ->and(end($lines))->toBe(str_pad('Viso', 39).'13.80 EUR');
});

it('includes non-kitchen categories that the kitchen receipt skips', function (int $category) {
    Order::create([
        'name' => json_encode([['Produktas' => 2.0]]),
        'amount' => 1,
        'category' => $category,
    ]);

    expect(implode("\n", fullReceiptLines()))->toContain('1 x Produktas');
})->with([
    'Kepiniai' => 2,
    'Gėrimai' => 3,
    'Alkoholis' => 7,
]);

it('adds packaging, takeaway and comments and counts packaging in the total', function () {
    Order::create([
        'name' => json_encode([['Kibinas' => 3.0]]),
        'amount' => 2,
        'category' => 2,
        'package' => 0.2,
        'takeaway' => true,
        'comments' => 'Be svogūnų',
    ]);

    $lines = fullReceiptLines();

    expect($lines)->toContain(str_pad('   + Pakuote', 44).'0.40')
        ->toContain('   Issinesimui')
        ->toContain('   Pageidavimai: Be svogunu')
        ->and(end($lines))->toBe(str_pad('Viso', 40).'6.40 EUR');
});

it('wraps long names and keeps every line within the printer width', function () {
    Order::create([
        'name' => json_encode([['Labai ilgas patiekalo pavadinimas, kuris netelpa vienoje eilutėje' => 4.5]]),
        'amount' => 1,
        'category' => 4,
    ]);

    $lines = fullReceiptLines();

    expect(collect($lines)->every(fn (string $line) => strlen($line) <= 48))->toBeTrue()
        ->and($lines)->toContain(str_pad('4.50', 48, ' ', STR_PAD_LEFT));
});
