<?php

use App\Printing\ReceiptCanvas;

it('draws the receipt at the printer width and grows with content', function () {
    $short = (new ReceiptCanvas)->text('Viso', 20)->toImage();
    $long = (new ReceiptCanvas)->text('Viso', 20)->separator()->text('1 x Pepsi', 17)->toImage();

    expect(imagesx($short))->toBe(ReceiptCanvas::WIDTH)
        ->and(imagesy($long))->toBeGreaterThan(imagesy($short));
});

it('draws Lithuanian letters with the bundled font', function () {
    $canvas = new ReceiptCanvas;

    expect($canvas->textWidth('ąčęėįšųūž ĄČĘĖĮŠŲŪŽ', 20))->toBeGreaterThan(0);

    $image = $canvas->text('ąčęėįšųūž', 40)->toImage();
    $darkPixels = 0;
    for ($x = 0; $x < imagesx($image); $x += 2) {
        for ($y = 0; $y < imagesy($image); $y += 2) {
            if ((imagecolorat($image, $x, $y) & 0xFF) < 128) {
                $darkPixels++;
            }
        }
    }

    expect($darkPixels)->toBeGreaterThan(50);
});

it('wraps long text by words to fit the width', function () {
    $canvas = new ReceiptCanvas;
    $lines = $canvas->wrap('Traškūs bulviniai blynai be mėsos su grietine ir spirgučiais', 24, 300);

    expect(count($lines))->toBeGreaterThan(1)
        ->and(collect($lines)->every(fn (string $line) => $canvas->textWidth($line, 24) <= 300))->toBeTrue()
        ->and(implode(' ', $lines))->toBe('Traškūs bulviniai blynai be mėsos su grietine ir spirgučiais');
});

it('breaks a word that is longer than the whole line', function () {
    $canvas = new ReceiptCanvas;
    $lines = $canvas->wrap(str_repeat('Ž', 60), 24, 300);

    expect(count($lines))->toBeGreaterThan(1)
        ->and(implode('', $lines))->toBe(str_repeat('Ž', 60));
});

it('keeps words in parentheses together when wrapping', function () {
    $canvas = new ReceiptCanvas;
    $width = $canvas->textWidth('1 x Vilkmergės CRAFT sidras (400', 20);
    $lines = $canvas->wrap('1 x Vilkmergės CRAFT sidras (400 ml)', 20, $width);

    expect($lines)->toBe(['1 x Vilkmergės CRAFT sidras', '(400 ml)']);
});

it('gives prices with the same number of digits the same width in a proportional font', function () {
    $canvas = new ReceiptCanvas(resource_path('fonts/Montserrat-Light.ttf'));

    expect($canvas->priceWidth('1.11', 20))->toBe($canvas->priceWidth('8.00', 20))
        ->and($canvas->textWidth('1.11', 20))->toBeLessThan($canvas->textWidth('8.00', 20));
});

it('draws the receipt picture at its own size in the middle', function () {
    $logo = imagecreatefrompng(resource_path('images/receipt-kibinas.png'));
    $image = (new ReceiptCanvas)->image(resource_path('images/receipt-kibinas.png'))->toImage();

    expect(imagesy($image))->toBe(imagesy($logo))
        ->and(imagesx($logo))->toBeLessThan(ReceiptCanvas::WIDTH);
});

it('puts an order line on several lines when the name does not fit its column', function () {
    $font = resource_path('fonts/Inter-Regular.ttf');
    $fonts = ['amount' => $font, 'name' => $font, 'price' => $font];

    $short = (new ReceiptCanvas)->line('1×', 'Kava', '2.50', 18, 54, $fonts)->toImage();
    $long = (new ReceiptCanvas)->line('1×', str_repeat('Baltasis sausas vynas ', 4), '5.50', 18, 54, $fonts)->toImage();

    expect(imagesy($long))->toBeGreaterThan(imagesy($short));
});

it('places group texts on their baselines inside a band of the given height', function () {
    $font = resource_path('fonts/Montserrat-Bold.ttf');
    $image = (new ReceiptCanvas)->group(60, [
        ['text' => 'UŽEIGA', 'font' => $font, 'size' => 20.0, 'align' => 'center', 'baseline' => 40],
        ['text' => '42', 'font' => $font, 'size' => 20.0, 'align' => 'right', 'baseline' => 40],
    ])->toImage();

    $darkBelowBaseline = 0;
    for ($x = 0; $x < ReceiptCanvas::WIDTH; $x++) {
        for ($y = 42; $y < 60; $y++) {
            if ((imagecolorat($image, $x, $y) & 0xFF) < 128) {
                $darkBelowBaseline++;
            }
        }
    }

    expect(imagesy($image))->toBe(60)
        ->and((imagecolorat($image, ReceiptCanvas::WIDTH - 10, 30) & 0xFF) < 128 || (imagecolorat($image, ReceiptCanvas::WIDTH - 12, 30) & 0xFF) < 128)->toBeTrue()
        ->and($darkBelowBaseline)->toBe(0);
});

it('draws a dashed separator with the given dash and gap', function () {
    $image = (new ReceiptCanvas)->separator(dashed: true, thickness: 2, dash: 6, gap: 3)->toImage();
    $dark = fn (int $x): bool => (imagecolorat($image, $x, 5) & 0xFF) < 128;

    expect($dark(0))->toBeTrue()
        ->and($dark(5))->toBeTrue()
        ->and($dark(6))->toBeFalse()
        ->and($dark(8))->toBeFalse()
        ->and($dark(9))->toBeTrue()
        ->and(imagesy($image))->toBe(13);
});
