<?php

use App\Console\Commands\PrinterTestLithuanian;

it('prints one line per character table and Baltic code page', function () {
    $lines = (new PrinterTestLithuanian)->testLines(0, 63);

    expect($lines)->toHaveCount(128);
});

it('selects the character table before printing the letters', function () {
    $lines = (new PrinterTestLithuanian)->testLines(51, 51);

    expect($lines[0])->toStartWith("\x1Bt\x33 51 / 1257: ")
        ->and($lines[1])->toStartWith("\x1Bt\x33 51 / 775: ");
});

it('encodes the Lithuanian letters correctly in each code page', function (int $index, string $codePage) {
    $line = (new PrinterTestLithuanian)->testLines(0, 0)[$index];
    $letters = substr($line, strpos($line, ': ') + 2, -1);

    expect(iconv($codePage, 'UTF-8', $letters))->toBe('ąčęėįšųūž ĄČĘĖĮŠŲŪŽ');
})->with([
    'Windows-1257' => [0, 'CP1257'],
    'PC775' => [1, 'CP775'],
]);

it('reports printers it cannot reach without failing', function () {
    $this->artisan('printer:test-lt', ['--ip' => ['127.0.0.1'], '--from' => 0, '--to' => 0])
        ->expectsOutputToContain("Couldn't print on 127.0.0.1")
        ->assertSuccessful();
});
