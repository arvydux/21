<?php

namespace App\Jobs;

use App\Models\PushSubscription;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

/**
 * Tells every browser following an order on the online board that it is ready.
 * Each subscription is used once and then removed.
 */
class SendOrderReadyPush
{
    use Dispatchable;

    public function __construct(public int $number) {}

    public function handle(WebPush $webPush): void
    {
        $subscriptions = PushSubscription::query()
            ->whereHas('orderNumber', fn ($query) => $query->where('number', $this->number)->where('is_ready', true))
            ->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $payload = json_encode([
            'title' => 'Prusakovų užeiga',
            'body' => "Užsakymas Nr. {$this->number} paruoštas! Atsiimkite prie prekystalio.",
            'number' => $this->number,
            'url' => '/online',
        ]);

        foreach ($subscriptions as $subscription) {
            $webPush->queueNotification(Subscription::create([
                'endpoint' => $subscription->endpoint,
                'publicKey' => $subscription->public_key,
                'authToken' => $subscription->auth_token,
                'contentEncoding' => 'aes128gcm',
            ]), $payload);
        }

        foreach ($webPush->flush() as $report) {
            if (! $report->isSuccess()) {
                Log::info('Order ready push failed', ['number' => $this->number, 'reason' => $report->getReason()]);
            }
        }

        PushSubscription::query()->whereKey($subscriptions->modelKeys())->delete();
    }
}
