<?php

use App\Http\Middleware\EnsureOnlineOrdersEnabled;
use App\Livewire\Orders\OrdersList;
use App\Models\Order;
use App\Models\OrderNumbers;
use Livewire\Livewire;
use Livewire\Volt\Volt;

function turnOnlineOrdersOff(): void
{
    cache()->forever(EnsureOnlineOrdersEnabled::CACHE_KEY, false);
}

it('lets customers follow their orders by default', function () {
    Volt::test('online-orders-settings')
        ->assertSet('enabled', true)
        ->assertSee('Kvite yra QR kodas');

    $this->get(route('online-orders'))->assertSuccessful();
});

it('remembers that order tracking is turned off', function () {
    Volt::test('online-orders-settings')
        ->set('enabled', false)
        ->assertSee('Kvite nėra QR kodo');

    expect(EnsureOnlineOrdersEnabled::isEnabled())->toBeFalse();

    Volt::test('online-orders-settings')
        ->assertSet('enabled', false);
});

it('shows the switch on the settings page', function () {
    $this->get(route('settings'))
        ->assertOk()
        ->assertSee('Užsakymo sekimas telefonu');
});

it('closes the customer order pages while tracking is off', function (string $method, string $route, bool $withToken) {
    $order = OrderNumbers::create(['number' => 47, 'is_ready' => false, 'by_phone' => false]);
    turnOnlineOrdersOff();

    $this->json($method, route($route, $withToken ? $order->token : []))->assertServiceUnavailable();
})->with([
    'board' => ['GET', 'online-orders', false],
    'board data' => ['GET', 'online-orders.data', false],
    'personal page' => ['GET', 'online-orders.order', true],
    'personal data' => ['GET', 'online-orders.order-data', true],
    'manifest' => ['GET', 'online-orders.order-manifest', true],
    'subscribe' => ['POST', 'online-orders.subscribe', false],
    'unsubscribe' => ['POST', 'online-orders.unsubscribe', false],
]);

it('leaves the QR code and its caption off the client receipt while tracking is off', function () {
    Order::create([
        'name' => json_encode([['Kibinas su mėsa' => 3.5]]),
        'amount' => 1,
        'category' => 1,
    ]);
    $component = Livewire::test(OrdersList::class)->instance();
    $link = 'https://status.prusakovuuzeiga.lt/online/AbCdE12345';

    $withQr = $component->fullClientReceipt(42, false, $link)->toImage();
    turnOnlineOrdersOff();
    $withoutQr = $component->fullClientReceipt(42, false, $link)->toImage();

    expect(imagesy($withoutQr))->toBeLessThan(imagesy($withQr) - 200)
        ->and(fn () => receiptQrLink($withoutQr))->toThrow(Exception::class);
});
