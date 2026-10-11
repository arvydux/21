<?php

use App\Livewire\Orders\OrdersList;
use App\Models\Order;
use Livewire\Livewire;

function fullReceiptLines(): array
{
    return Livewire::test(OrdersList::class)->instance()->fullReceiptLines();
}

function receiptLine(string $text, ?string $price = null, int $indent = 0): array
{
    return ['text' => $text, 'price' => $price, 'indent' => $indent];
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

    expect(fullReceiptLines())->toBe([
        receiptLine('2 x Čeburekas su kiauliena', '11.00'),
        receiptLine('+ Sūris', '1.00', 30),
        receiptLine('1 x Pepsi', '1.80'),
        receiptLine('Viso', '13.80 EUR'),
    ]);
});

it('includes non-kitchen categories that the kitchen receipt skips', function (int $category) {
    Order::create([
        'name' => json_encode([['Produktas' => 2.0]]),
        'amount' => 1,
        'category' => $category,
    ]);

    expect(fullReceiptLines()[0])->toBe(receiptLine('1 x Produktas', '2.00'));
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

    expect(fullReceiptLines())->toBe([
        receiptLine('2 x Kibinas', '6.00'),
        receiptLine('+ Pakuotė', '0.40', 30),
        receiptLine('Išsinešimui', null, 30),
        receiptLine('Pageidavimai: Be svogūnų', null, 30),
        receiptLine('Viso', '6.40 EUR'),
    ]);
});

it('keeps Lithuanian letters and quotes as they are', function () {
    Order::create([
        'name' => json_encode([['Viskis JACK DANIEL’S (500 ml)' => 40.0]]),
        'amount' => 1,
        'category' => 7,
    ]);

    expect(fullReceiptLines()[0]['text'])->toBe('1 x Viskis JACK DANIEL’S (500 ml)');
});
