<?php

use App\Livewire\ClientOrders;
use App\Models\OrderNumbers;
use Livewire\Livewire;
use Livewire\Volt\Volt;

beforeEach(fn () => cache()->forever('pos_template', 'uzeiga'));

it('shows preparing and ready numbers in the Užeiga colours', function () {
    OrderNumbers::create(['number' => 61, 'is_ready' => false, 'by_phone' => true, 'is_taken' => false]);
    OrderNumbers::create(['number' => 52, 'is_ready' => true, 'by_phone' => false, 'is_taken' => false]);
    OrderNumbers::create(['number' => 40, 'is_ready' => true, 'by_phone' => false, 'is_taken' => true]);

    $html = Livewire::test(ClientOrders::class)
        ->assertSeeHtml('data-pickup-uzeiga')
        ->assertSee('Prusakovų užeiga')
        ->assertSee('Gaminami')
        ->assertSee('Paruošti — atsiimkite')
        ->assertDontSeeHtml('M3 5a2 2 0 012-2h3.28')
        ->html();

    $preparing = str($html)->between('data-pickup-preparing', 'data-pickup-ready')->toString();
    $ready = str($html)->after('data-pickup-ready')->toString();

    expect($preparing)->toContain('>61<')
        ->and($preparing)->toContain('bg-[#F3ECDF] text-[#2A2620]')
        ->and($ready)->toContain('data-order-number="52"')
        ->and($ready)->toContain('bg-[#14664A] text-white')
        ->and($html)->not->toContain('data-order-number="40"');
});

it('shows an empty state for a column without orders in the Užeiga template', function (bool $isReady, string $emptyText, string $otherEmptyText) {
    OrderNumbers::create(['number' => 61, 'is_ready' => $isReady, 'by_phone' => false, 'is_taken' => false]);

    Livewire::test(ClientOrders::class)
        ->assertSee($emptyText)
        ->assertDontSee($otherEmptyText);
})->with([
    'nothing ready' => [false, 'Paruoštų užsakymų dar nėra', 'Šiuo metu nieko negaminame'],
    'nothing preparing' => [true, 'Šiuo metu nieko negaminame', 'Paruoštų užsakymų dar nėra'],
]);

it('fits many orders on the screen in the Užeiga template', function () {
    foreach (range(1, 25) as $number) {
        OrderNumbers::create(['number' => $number, 'is_ready' => false, 'by_phone' => false, 'is_taken' => false]);
    }

    $html = Livewire::test(ClientOrders::class)
        ->assertSeeHtml('--pickup-cols: repeat(5, minmax(0, 1fr))')
        ->html();

    expect(preg_match_all('/" data-pickup-tile\s/', $html))->toBe(25)
        ->and($html)->toContain('fit()');
});

it('offers the Užeiga template in the settings picker', function () {
    cache()->forget('pos_template');

    Volt::test('template-picker')
        ->assertSee('Užeiga')
        ->set('template', 'uzeiga');

    expect(cache('pos_template'))->toBe('uzeiga');
});
