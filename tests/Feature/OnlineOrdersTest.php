<?php

use App\Models\OrderNumbers;

function onlineOrder(int $number, bool $isReady = false, bool $isTaken = false): OrderNumbers
{
    return OrderNumbers::create(['number' => $number, 'is_ready' => $isReady, 'by_phone' => false, 'is_taken' => $isTaken]);
}

it('shows preparing and ready numbers on the online board', function () {
    onlineOrder(61);
    onlineOrder(52, isReady: true)->forceFill(['updated_at' => now()->subMinutes(5)])->save();
    onlineOrder(53, isReady: true)->forceFill(['updated_at' => now()->subMinute()])->save();
    onlineOrder(40, isReady: true, isTaken: true);

    $html = $this->get(route('online-orders'))
        ->assertSuccessful()
        ->assertSee('Paruošti')
        ->assertSee('Gaminami')
        ->getContent();

    $ready = str($html)->between('data-online-ready>', 'data-online-preparing>')->toString();
    $preparing = str($html)->after('data-online-preparing>')->toString();

    expect($ready)->toContain('data-order-number="53"')
        ->and($ready)->toContain('data-order-number="52"')
        ->and(strpos($ready, '"53"'))->toBeLessThan(strpos($ready, '"52"'))
        ->and($ready)->not->toContain('data-order-number="61"')
        ->and($preparing)->toContain('data-order-number="61"')
        ->and($html)->not->toContain('data-order-number="40"');
});

it('shows empty states when there are no orders', function () {
    $this->get(route('online-orders'))
        ->assertSuccessful()
        ->assertSee('Paruoštų užsakymų dar nėra')
        ->assertSee('Šiuo metu nieko negaminame');
});

it('returns only order numbers from the data endpoint', function () {
    onlineOrder(61);
    onlineOrder(62);
    onlineOrder(53, isReady: true);
    onlineOrder(40, isReady: true, isTaken: true);

    $response = $this->getJson(route('online-orders.data'))
        ->assertSuccessful()
        ->assertExactJson(['preparing' => [61, 62], 'ready' => [53]]);

    expect($response->headers->get('Cache-Control'))->toContain('no-store');
});

it('lets tunnel visitors reach only the online board', function (string $path) {
    $this->get($path, ['CF-Connecting-IP' => '203.0.113.7'])->assertNotFound();
})->with(['kasa' => '/kasa', 'kitchen' => '/kitchen', 'menu' => '/menu', 'settings' => '/settings', 'orders' => '/orders', 'raw orders api' => '/get-unready-orders', 'login' => '/login', 'livewire script' => '/livewire/livewire.js']);

it('serves the online board to tunnel visitors', function () {
    onlineOrder(53, isReady: true);

    $this->get('/online', ['CF-Connecting-IP' => '203.0.113.7'])->assertSuccessful()->assertSee('data-order-number="53"', false);
    $this->getJson('/online/data', ['CF-Connecting-IP' => '203.0.113.7'])->assertSuccessful()->assertJson(['ready' => [53]]);
});

it('sends tunnel visitors from the home page to the online board', function () {
    $this->get('/', ['CF-Connecting-IP' => '203.0.113.7'])->assertRedirect('/online');
});

it('treats the public host as the tunnel even without cloudflare headers', function () {
    config(['online-orders.public_host' => 'status.example.lt']);

    $this->get('http://status.example.lt/kasa')->assertNotFound();
    $this->get('http://status.example.lt/online')->assertSuccessful();
});

it('rejects anything but reads from tunnel visitors', function () {
    $this->post('/online', [], ['CF-Connecting-IP' => '203.0.113.7'])->assertNotFound();
    $this->post('/livewire/update', [], ['CF-Connecting-IP' => '203.0.113.7'])->assertNotFound();
});

it('keeps the local network unrestricted', function () {
    $this->get('/kitchen')->assertSuccessful();
});
