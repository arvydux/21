<?php

use App\Jobs\SendOrderReadyPush;
use App\Models\OrderNumbers;
use App\Models\PushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

use function Pest\Laravel\mock;

function personalOrder(int $number, bool $isReady = false, bool $isTaken = false): OrderNumbers
{
    return OrderNumbers::create(['number' => $number, 'is_ready' => $isReady, 'by_phone' => false, 'is_taken' => $isTaken]);
}

it('gives every order its own random link code', function () {
    $first = personalOrder(43);
    $second = personalOrder(43);

    expect($first->token)->toMatch('/^[A-Za-z0-9]{10}$/')
        ->and($second->token)->not->toBe($first->token)
        ->and($first->statusUrl())->toBe('https://status.prusakovuuzeiga.lt/online/'.$first->token);
});

it('shows the order number and its state on the personal page', function (bool $isReady, bool $isTaken, string $state, string $text) {
    $order = personalOrder(43, $isReady, $isTaken);

    $this->get(route('online-orders.order', $order->token))
        ->assertSuccessful()
        ->assertSee('data-status="'.$order->status().'"', false)
        ->assertSee('43')
        ->assertSee($state)
        ->assertSee($text)
        ->assertSee(route('online-orders.order-manifest', $order->token, absolute: false));
})->with([
    'preparing' => [false, false, 'Gaminamas', 'Pranešime, kai bus paruoštas.'],
    'ready' => [true, false, 'Paruoštas', 'Atsiimkite prie prekystalio.'],
    'taken' => [true, true, 'Atsiimtas', 'Ačiū ir skanaus!'],
]);

it('follows only its own order when another order has the same number', function () {
    $old = personalOrder(43, isReady: true, isTaken: true);
    $current = personalOrder(43);

    $this->getJson(route('online-orders.order-data', $old->token))->assertExactJson(['number' => 43, 'status' => 'taken']);
    $this->getJson(route('online-orders.order-data', $current->token))->assertExactJson(['number' => 43, 'status' => 'preparing']);
});

it('returns the state of the order from the data endpoint', function () {
    $order = personalOrder(43);

    $response = $this->getJson(route('online-orders.order-data', $order->token))
        ->assertSuccessful()
        ->assertExactJson(['number' => 43, 'status' => 'preparing']);

    expect($response->headers->get('Cache-Control'))->toContain('no-store');

    $order->update(['is_ready' => true]);

    $this->getJson(route('online-orders.order-data', $order->token))->assertExactJson(['number' => 43, 'status' => 'ready']);
});

it('explains that the receipt is no longer valid when the order is gone', function () {
    $this->get('/online/AbCdE12345')
        ->assertNotFound()
        ->assertSee('Nerastas')
        ->assertSee('Šis kvitas nebegalioja.');

    $this->getJson('/online/data/AbCdE12345')->assertNotFound()->assertExactJson(['status' => 'missing']);
});

it('ignores links that do not look like an order code', function (string $path) {
    $this->get($path)->assertNotFound()->assertDontSee('Šis kvitas nebegalioja.');
})->with(['too short' => '/online/abc', 'too long' => '/online/AbCdE123456', 'symbols' => '/online/AbCdE-1234']);

it('keeps the general board data endpoint working', function () {
    personalOrder(61);

    $this->getJson(route('online-orders.data'))->assertExactJson(['preparing' => [61], 'ready' => []]);
});

it('starts the home screen app on the personal page', function () {
    $order = personalOrder(43);

    $this->getJson(route('online-orders.order-manifest', $order->token))
        ->assertSuccessful()
        ->assertHeader('Content-Type', 'application/manifest+json')
        ->assertJsonPath('start_url', '/online/'.$order->token)
        ->assertJsonPath('display', 'standalone');
});

it('lets tunnel visitors open the personal page, its data and manifest', function () {
    $order = personalOrder(43);
    $tunnel = ['CF-Connecting-IP' => '203.0.113.7'];

    $this->get('/online/'.$order->token, $tunnel)->assertSuccessful();
    $this->get('/online/data/'.$order->token, $tunnel)->assertSuccessful();
    $this->get('/online/'.$order->token.'/manifest.webmanifest', $tunnel)->assertSuccessful();
    $this->get('/online/'.$order->token.'/other', $tunnel)->assertNotFound();
});

it('subscribes a browser to the order of the link', function () {
    $old = personalOrder(43, isReady: true, isTaken: true);
    $current = personalOrder(43);

    $this->postJson(route('online-orders.subscribe'), ['token' => $current->token, ...browserSubscription()])->assertCreated();

    expect($current->pushSubscriptions()->count())->toBe(1)
        ->and($old->pushSubscriptions()->count())->toBe(0);
});

it('refuses to subscribe by link to an order that is ready or gone', function () {
    $ready = personalOrder(43, isReady: true);

    $this->postJson(route('online-orders.subscribe'), ['token' => $ready->token, ...browserSubscription()])->assertUnprocessable();
    $this->postJson(route('online-orders.subscribe'), ['token' => 'AbCdE12345', ...browserSubscription()])->assertUnprocessable();
});

it('validates the order link code', function (mixed $token) {
    $this->postJson(route('online-orders.subscribe'), ['token' => $token, ...browserSubscription()])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('token');
})->with(['too short' => 'abc', 'symbols' => 'AbCdE-1234', 'not text' => [[1, 2]]]);

it('opens the personal page from the order ready push, or the board for orders made before links', function (bool $hasLink) {
    $order = personalOrder(43, isReady: true);
    if (! $hasLink) {
        $order->forceFill(['token' => null])->save();
    }
    PushSubscription::factory()->for($order, 'orderNumber')->create();

    $sent = [];
    mock(WebPush::class)
        ->shouldReceive('queueNotification')->once()
        ->andReturnUsing(function (Subscription $subscription, string $payload) use (&$sent): void {
            $sent[] = json_decode($payload, true);
        })
        ->shouldReceive('flush')->once()
        ->andReturnUsing(fn () => yield from []);

    (new SendOrderReadyPush(43))->handle(app(WebPush::class));

    expect($sent[0]['url'])->toBe($hasLink ? '/online/'.$order->token : '/online');
})->with(['with a link' => true, 'made before links' => false]);
