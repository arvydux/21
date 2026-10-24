<?php

use App\Jobs\SendOrderReadyPush;
use App\Livewire\Kitchen;
use App\Models\OrderNumbers;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

use function Pest\Laravel\mock;

function pushOrder(int $number, bool $isReady = false, bool $isTaken = false): OrderNumbers
{
    return OrderNumbers::create(['number' => $number, 'is_ready' => $isReady, 'by_phone' => false, 'is_taken' => $isTaken]);
}

it('subscribes a browser to a preparing order', function () {
    $order = pushOrder(47);

    $this->postJson(route('online-orders.subscribe'), ['number' => 47, ...browserSubscription()])
        ->assertCreated()
        ->assertJson(['subscribed' => true]);

    expect($order->pushSubscriptions()->sole())
        ->endpoint->toBe('https://fcm.googleapis.com/fcm/send/abc')
        ->public_key->toBe('public-key')
        ->auth_token->toBe('auth-token');
});

it('moves a browser to the newly followed order', function () {
    pushOrder(47);
    $second = pushOrder(48);

    $this->postJson(route('online-orders.subscribe'), ['number' => 47, ...browserSubscription()])->assertCreated();
    $this->postJson(route('online-orders.subscribe'), ['number' => 48, ...browserSubscription()])->assertCreated();

    expect(PushSubscription::count())->toBe(1)
        ->and(PushSubscription::sole()->order_number_id)->toBe($second->id);
});

it('subscribes to the current order when an old taken order shares the number', function () {
    pushOrder(47, isReady: true, isTaken: true);
    $current = pushOrder(47);

    $this->postJson(route('online-orders.subscribe'), ['number' => 47, ...browserSubscription()])->assertCreated();

    expect(PushSubscription::sole()->order_number_id)->toBe($current->id);
});

it('refuses to subscribe to an order that is ready or missing', function (int $number) {
    pushOrder(50, isReady: true);

    $this->postJson(route('online-orders.subscribe'), ['number' => $number, ...browserSubscription()])
        ->assertUnprocessable();

    expect(PushSubscription::count())->toBe(0);
})->with(['ready order' => 50, 'missing order' => 99]);

it('validates the browser subscription', function (array $payload, string $field) {
    pushOrder(47);

    $this->postJson(route('online-orders.subscribe'), $payload)->assertJsonValidationErrors($field);
})->with([
    'no number' => [browserSubscription(), 'number'],
    'no endpoint' => [['number' => 47, 'keys' => ['p256dh' => 'a', 'auth' => 'b']], 'endpoint'],
    'insecure endpoint' => [['number' => 47, ...browserSubscription('http://push.example/abc')], 'endpoint'],
    'no keys' => [['number' => 47, 'endpoint' => 'https://push.example/abc'], 'keys.p256dh'],
]);

it('unsubscribes a browser', function () {
    PushSubscription::factory()->create(['endpoint' => 'https://push.example/abc']);
    PushSubscription::factory()->create(['endpoint' => 'https://push.example/other']);

    $this->postJson(route('online-orders.unsubscribe'), ['endpoint' => 'https://push.example/abc'])->assertSuccessful();

    expect(PushSubscription::pluck('endpoint')->all())->toBe(['https://push.example/other']);
});

it('lets tunnel visitors subscribe and unsubscribe', function () {
    pushOrder(47);
    $tunnel = ['CF-Connecting-IP' => '203.0.113.7'];

    $this->postJson('/online/subscribe', ['number' => 47, ...browserSubscription()], $tunnel)->assertCreated();
    $this->postJson('/online/unsubscribe', ['endpoint' => 'https://fcm.googleapis.com/fcm/send/abc'], $tunnel)->assertSuccessful();
});

it('sends the push after the kitchen marks the order ready', function () {
    Bus::fake();
    pushOrder(47);

    Livewire::test(Kitchen::class)->call('makeOrderReady', 47);

    Bus::assertDispatchedAfterResponse(SendOrderReadyPush::class, fn (SendOrderReadyPush $job): bool => $job->number === 47);
});

it('pushes to every follower of a ready order once and forgets them', function () {
    $ready = pushOrder(47, isReady: true);
    $other = pushOrder(48);
    PushSubscription::factory()->count(2)->for($ready, 'orderNumber')->create();
    PushSubscription::factory()->for($other, 'orderNumber')->create();

    $sent = [];
    mock(WebPush::class)
        ->shouldReceive('queueNotification')->twice()
        ->andReturnUsing(function (Subscription $subscription, string $payload) use (&$sent): void {
            $sent[] = json_decode($payload, true);
        })
        ->shouldReceive('flush')->once()
        ->andReturnUsing(fn () => yield from []);

    (new SendOrderReadyPush(47))->handle(app(WebPush::class));

    expect($sent)->toHaveCount(2)
        ->and($sent[0]['body'])->toContain('Nr. 47 paruoštas')
        ->and($sent[0]['url'])->toBe('/online/'.$ready->token)
        ->and(PushSubscription::count())->toBe(1)
        ->and(PushSubscription::sole()->order_number_id)->toBe($other->id);
});

it('forgets followers even when the push service rejects them', function () {
    $ready = pushOrder(47, isReady: true);
    PushSubscription::factory()->for($ready, 'orderNumber')->create();

    $report = Mockery::mock(MessageSentReport::class);
    $report->shouldReceive('isSuccess')->andReturnFalse();
    $report->shouldReceive('getReason')->andReturn('410 Gone');

    mock(WebPush::class)
        ->shouldReceive('queueNotification')->once()
        ->shouldReceive('flush')->once()
        ->andReturnUsing(fn () => yield $report);

    (new SendOrderReadyPush(47))->handle(app(WebPush::class));

    expect(PushSubscription::count())->toBe(0);
});

it('does not push for an order that is not ready', function () {
    $order = pushOrder(47);
    PushSubscription::factory()->for($order, 'orderNumber')->create();

    mock(WebPush::class)->shouldNotReceive('queueNotification', 'flush');

    (new SendOrderReadyPush(47))->handle(app(WebPush::class));

    expect(PushSubscription::count())->toBe(1);
});

it('drops followers when their order number is recycled', function () {
    $order = pushOrder(47, isReady: true, isTaken: true);
    PushSubscription::factory()->for($order, 'orderNumber')->create();

    $order->delete();

    expect(PushSubscription::count())->toBe(0);
});

it('offers the push button and home screen hint on the board', function () {
    $this->get(route('online-orders'))
        ->assertSuccessful()
        ->assertSee('rel="manifest"', false)
        ->assertSee('data-notify', false)
        ->assertSee(config('online-orders.vapid.public_key'), false);
});

it('makes the phone ring again when a notification for the same order is shown twice', function () {
    $serviceWorker = file_get_contents(public_path('online-sw.js'));

    expect($serviceWorker)->toContain('renotify: true')
        ->and($serviceWorker)->toContain('silent: false')
        ->and($serviceWorker)->toContain('getNotifications({ tag })')
        ->and($serviceWorker)->toContain('notification.close()');
});

it('puts a new service worker to work as soon as the phone downloads it', function (string $page) {
    $serviceWorker = file_get_contents(public_path('online-sw.js'));

    expect($serviceWorker)->toContain('self.skipWaiting()')
        ->and($serviceWorker)->toContain('self.clients.claim()');

    $this->get($page === 'board' ? route('online-orders') : route('online-orders.order', pushOrder(47)->token))
        ->assertSee('registration?.update()', false);
})->with(['board', 'personal page']);

it('asks the push service to wake a sleeping phone right away', function () {
    $ready = pushOrder(47, isReady: true);
    PushSubscription::factory()->for($ready, 'orderNumber')->create();

    $options = null;
    mock(WebPush::class)
        ->shouldReceive('queueNotification')->once()
        ->andReturnUsing(function (Subscription $subscription, string $payload, array $sentOptions) use (&$options): void {
            $options = $sentOptions;
        })
        ->shouldReceive('flush')->once()
        ->andReturnUsing(fn () => yield from []);

    (new SendOrderReadyPush(47))->handle(app(WebPush::class));

    expect($options)->toBe(['urgency' => 'high', 'TTL' => 1800]);
});
