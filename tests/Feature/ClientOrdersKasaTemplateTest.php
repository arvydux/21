<?php

use App\Livewire\ClientOrders;
use App\Models\OrderNumbers;
use Livewire\Livewire;
use Livewire\Volt\Volt;

beforeEach(fn () => cache()->forever('pos_template', 'kasa'));

it('shows preparing and ready numbers on the pickup screen as equal tiles', function () {
    OrderNumbers::create(['number' => 61, 'is_ready' => false, 'by_phone' => true, 'is_taken' => false]);
    OrderNumbers::create(['number' => 52, 'is_ready' => true, 'by_phone' => false, 'is_taken' => false])
        ->forceFill(['updated_at' => now()->subMinutes(5)])->save();
    OrderNumbers::create(['number' => 53, 'is_ready' => true, 'by_phone' => false, 'is_taken' => false])
        ->forceFill(['updated_at' => now()->subMinutes(1)])->save();
    OrderNumbers::create(['number' => 40, 'is_ready' => true, 'by_phone' => false, 'is_taken' => true]);

    $html = Livewire::test(ClientOrders::class)
        ->assertSeeHtml('data-pickup-screen')
        ->assertSee('Gaminami')
        ->assertSee('Gaminamas')
        ->assertSee('Paruošti')
        ->html();

    $preparing = str($html)->between('data-pickup-preparing', 'data-pickup-ready')->toString();
    $ready = str($html)->after('data-pickup-ready')->toString();

    expect($preparing)->toContain('>61<')
        ->and($ready)->toContain('data-order-number="53"')
        ->and($ready)->not->toContain('col-span')
        ->and($html)->not->toContain('ready-flash')
        ->and($ready)->toContain('data-order-number="52"')
        ->and($ready)->not->toContain('data-order-number="40"');
});

it('shows friendly empty states when a column has no orders', function () {
    OrderNumbers::create(['number' => 61, 'is_ready' => false, 'by_phone' => false, 'is_taken' => false]);

    Livewire::test(ClientOrders::class)
        ->assertSee('Paruoštų užsakymų dar nėra')
        ->assertDontSee('Šiuo metu nieko negaminame');
});

it('offers the new pickup template in the settings picker', function () {
    Volt::test('template-picker')
        ->assertSee('Kasa')
        ->set('template', 'kasa');

    expect(cache('pos_template'))->toBe('kasa');
});

it('lets the browser shrink the tiles so that every order fits on the screen', function () {
    foreach (range(1, 25) as $number) {
        OrderNumbers::create(['number' => $number, 'is_ready' => false, 'by_phone' => false, 'is_taken' => false]);
    }

    $html = Livewire::test(ClientOrders::class)
        ->assertSeeHtml('--pickup-cols: repeat(5, minmax(0, 1fr))')
        ->assertSeeHtml('wire:ignore.self')
        ->html();

    expect(substr_count($html, 'data-pickup-grid class="grid grid-cols-(--pickup-cols)'))->toBe(1)
        ->and(preg_match_all('/" data-pickup-tile\s/', $html))->toBe(25)
        ->and($html)->toContain('fit()');
});

it('switches between flat and shaded tiles with the two Kasa templates', function (string $template, string $amberTile, string $greenTile) {
    cache()->forever('pos_template', $template);
    OrderNumbers::create(['number' => 61, 'is_ready' => false, 'by_phone' => false, 'is_taken' => false]);
    OrderNumbers::create(['number' => 52, 'is_ready' => true, 'by_phone' => false, 'is_taken' => false]);

    Livewire::test(ClientOrders::class)
        ->assertSeeHtml('data-pickup-screen')
        ->assertSeeHtml($amberTile)
        ->assertSeeHtml($greenTile);
})->with([
    'flat' => ['kasa', 'rounded-3xl bg-[#fdbf2d]', 'rounded-3xl bg-[#169147]'],
    'gradient' => ['kasa-gradient', 'from-[#ffd35c] to-[#eba521]', 'from-[#23805a] to-[#145a3e]'],
]);

it('offers both Kasa templates in the settings picker', function () {
    Volt::test('template-picker')
        ->assertSee('Kasa gradientas')
        ->set('template', 'kasa-gradient');

    expect(cache('pos_template'))->toBe('kasa-gradient');
});
