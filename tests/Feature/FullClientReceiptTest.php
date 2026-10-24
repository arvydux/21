<?php

use App\Livewire\Orders\OrdersList;
use App\Models\Order;
use chillerlan\QRCode\Common\GDLuminanceSource;
use chillerlan\QRCode\QRCode;
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

it('shows free toppings without a price', function () {
    Order::create([
        'name' => json_encode([['Žemaičių blynai' => 7.4]]),
        'toppings' => json_encode([['Grietinė' => 0]]),
        'amount' => 1,
        'category' => 5,
    ]);

    expect(fullReceiptLines())->toBe([
        receiptLine('1 x Žemaičių blynai', '7.40'),
        receiptLine('+ Grietinė', null, 30),
        receiptLine('Viso', '7.40 EUR'),
    ]);
});

it('draws the full client receipt with and without the phone mark', function () {
    Order::create([
        'name' => json_encode([['Baltasis sausas vynas „COLPASSO“ (750 ml)' => 22.0]]),
        'amount' => 1,
        'category' => 7,
    ]);
    $component = Livewire::test(OrdersList::class)->instance();

    $plain = $component->fullClientReceipt(42, false)->toImage();
    $byPhone = $component->fullClientReceipt(42, true)->toImage();

    expect(imagesx($plain))->toBe(App\Printing\ReceiptCanvas::WIDTH)
        ->and(imagesy($byPhone))->toBe(imagesy($plain))
        ->and(darkPixels($byPhone))->toBeGreaterThan(darkPixels($plain))
        ->and(darkPixels(imagecrop($plain, ['x' => 0, 'y' => 0, 'width' => 576, 'height' => 200])))->toBeGreaterThan(1000);
});

function darkPixels(GdImage $image): int
{
    $dark = 0;
    for ($x = 0; $x < imagesx($image); $x++) {
        for ($y = 0; $y < imagesy($image); $y++) {
            if ((imagecolorat($image, $x, $y) & 0xFF) < 128) {
                $dark++;
            }
        }
    }

    return $dark;
}

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

it('prints two copies of the full receipt for the client', function () {
    expect(OrdersList::CLIENT_RECEIPT_COPIES)->toBe(2);
});

/**
 * Reads the QR code printed at the bottom of a receipt.
 */
function receiptQrLink(GdImage $receipt): string
{
    $bottom = imagecrop($receipt, ['x' => 0, 'y' => imagesy($receipt) - 300, 'width' => 576, 'height' => 300]);

    return (new QRCode)->readFromSource(new GDLuminanceSource($bottom))->data;
}

it('ends the client receipt with the QR code of the personal order page', function () {
    Order::create([
        'name' => json_encode([['Kava Cappuccino' => 2.5]]),
        'amount' => 1,
        'category' => 3,
    ]);
    $link = 'https://status.prusakovuuzeiga.lt/online/AbCdE12345';

    $receipt = Livewire::test(OrdersList::class)->instance()->fullClientReceipt(42, false, $link)->toImage();

    expect(receiptQrLink($receipt))->toBe($link);
});

it('links receipts without an order number to the general board', function () {
    Order::create([
        'name' => json_encode([['Kava Cappuccino' => 2.5]]),
        'amount' => 1,
        'category' => 3,
    ]);

    $receipt = Livewire::test(OrdersList::class)->instance()->fullClientReceipt(null, false)->toImage();

    expect(receiptQrLink($receipt))->toBe('https://status.prusakovuuzeiga.lt/online');
});
