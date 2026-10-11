<?php

use App\Printing\ReceiptCanvas;

it('draws the receipt at the printer width and grows with content', function () {
    $short = (new ReceiptCanvas)->text('Viso', 20)->toImage();
    $long = (new ReceiptCanvas)->text('Viso', 20)->separator()->row('1 x Pepsi', '1.80', 17)->toImage();

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
