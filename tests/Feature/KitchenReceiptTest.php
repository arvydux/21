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

/**
 * @return list<array{text: string, font: string, size: float, align: string, baseline: int, framed?: bool}>
 */
function kitchenReceiptHeaderTexts(bool $byPhone): array
{
    $receipt = Livewire::test(OrdersList::class)->instance()->kitchenReceipt(42, $byPhone);

    return collect((fn () => $this->blocks)->call($receipt))->firstWhere('type', 'group')['texts'];
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

it('prints a framed TELEFONU in the middle of the header of a phone order', function () {
    $phoneMark = collect(kitchenReceiptHeaderTexts(true))->firstWhere('text', 'TELEFONU');

    expect($phoneMark['align'])->toBe('center')
        ->and($phoneMark['framed'])->toBeTrue();
});

it('prints no phone mark on a counter order and shows the date on both', function () {
    expect(collect(kitchenReceiptHeaderTexts(false))->pluck('text'))->not->toContain('TELEFONU');

    foreach ([false, true] as $byPhone) {
        expect(collect(kitchenReceiptHeaderTexts($byPhone))->pluck('text'))->toContain(now('Europe/Vilnius')->format('Y-m-d'));
    }
});

it('keeps the framed TELEFONU clear of the widest order number and time, inside the header', function () {
    $this->travelTo(now('Europe/Vilnius')->setTime(0, 0));

    $component = Livewire::test(OrdersList::class)->instance();
    $counter = $component->kitchenReceipt(88, false)->toImage();
    $phone = $component->kitchenReceipt(88, true)->toImage();

    expect(imagesy($phone))->toBe(imagesy($counter));

    $added = ['left' => PHP_INT_MAX, 'right' => 0, 'top' => PHP_INT_MAX, 'bottom' => 0];

    for ($y = 0; $y < imagesy($phone); $y++) {
        for ($x = 0; $x < imagesx($phone); $x++) {
            $wasDark = (imagecolorat($counter, $x, $y) & 0xFF) < 128;
            $isDark = (imagecolorat($phone, $x, $y) & 0xFF) < 128;

            expect($wasDark && ! $isDark)->toBeFalse();

            if ($isDark && ! $wasDark) {
                $added = [
                    'left' => min($added['left'], $x),
                    'right' => max($added['right'], $x),
                    'top' => min($added['top'], $y),
                    'bottom' => max($added['bottom'], $y),
                ];
            }
        }
    }

    $counterHeader = collect(range(0, imagesx($counter) - 1))
        ->filter(fn (int $x): bool => collect(range($added['top'], $added['bottom']))->contains(fn (int $y): bool => (imagecolorat($counter, $x, $y) & 0xFF) < 128))
        ->values();
    $numberRight = $counterHeader->filter(fn (int $x): bool => $x < 288)->max();
    $timeLeft = $counterHeader->filter(fn (int $x): bool => $x >= 288)->min();

    expect($added['left'])->toBeGreaterThan($numberRight + 10)
        ->and($added['right'])->toBeLessThan($timeLeft - 10)
        ->and($added['top'])->toBeGreaterThanOrEqual(OrdersList::KITCHEN_TOP_MARGIN)
        ->and($added['bottom'])->toBeLessThan(OrdersList::KITCHEN_TOP_MARGIN + 112);
});

it('prints kitchen order lines larger than before', function () {
    expect(OrdersList::KITCHEN_ITEM_SIZE)->toBeGreaterThan(24);
});
