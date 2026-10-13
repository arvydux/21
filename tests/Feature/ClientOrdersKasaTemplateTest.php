<?php

use App\Livewire\ClientOrders;
use App\Models\OrderNumbers;
use Livewire\Livewire;
use Livewire\Volt\Volt;

beforeEach(fn () => cache()->forever('pos_template', 'kasa'));

it('shows preparing and ready numbers on the pickup screen with the newest ready one on top', function () {
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
        ->assertSee('Paruoštas atsiimti')
        ->html();

    $preparing = str($html)->between('data-pickup-preparing', 'data-pickup-ready')->toString();
    $ready = str($html)->after('data-pickup-ready')->toString();

    expect($preparing)->toContain('>61<')
        ->and($ready)->toMatch('/data-order-number="53" data-newest-ready/')
        ->and($ready)->toContain('data-order-number="52"')
        ->and($ready)->not->toContain('data-order-number="40"');
});

it('flashes a ready number only during its first 10 seconds', function () {
    OrderNumbers::create(['number' => 58, 'is_ready' => true, 'by_phone' => false, 'is_taken' => false])
        ->forceFill(['updated_at' => now()->subSeconds(4)])->save();
    OrderNumbers::create(['number' => 57, 'is_ready' => true, 'by_phone' => false, 'is_taken' => false])
        ->forceFill(['updated_at' => now()->subSeconds(30)])->save();

    $html = Livewire::test(ClientOrders::class)->html();

    expect($html)->toMatch('/data-order-number="58"[^>]*ready-flash[^>]*style="animation-delay: -4\.\d+s"/')
        ->and($html)->not->toMatch('/data-order-number="57"[^>]*ready-flash/');
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
