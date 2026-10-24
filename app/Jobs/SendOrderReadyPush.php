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

    /**
     * High urgency wakes a sleeping phone at once instead of waiting until it is unlocked;
     * a notice older than half an hour is no longer useful.
     *
     * @var array{urgency: string, TTL: int}
     */
    private const DELIVERY = ['urgency' => 'high', 'TTL' => 1800];

    public function __construct(public int $number) {}

    public function handle(WebPush $webPush): void
    {
        $subscriptions = PushSubscription::query()
            ->with('orderNumber')
            ->whereHas('orderNumber', fn ($query) => $query->where('number', $this->number)->where('is_ready', true))
            ->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        foreach ($subscriptions as $subscription) {
            $token = $subscription->orderNumber->token;

            $webPush->queueNotification(Subscription::create([
                'endpoint' => $subscription->endpoint,
                'publicKey' => $subscription->public_key,
                'authToken' => $subscription->auth_token,
                'contentEncoding' => 'aes128gcm',
            ]), json_encode([
                'title' => 'Prusakovų užeiga',
                'body' => "Užsakymas Nr. {$this->number} paruoštas! Atsiimkite prie prekystalio.",
                'number' => $this->number,
                'url' => $token === null ? '/online' : route('online-orders.order', $token, absolute: false),
            ]), self::DELIVERY);
        }

        foreach ($webPush->flush() as $report) {
            if (! $report->isSuccess()) {
                Log::info('Order ready push failed', ['number' => $this->number, 'reason' => $report->getReason()]);
            }
        }

        PushSubscription::query()->whereKey($subscriptions->modelKeys())->delete();
    }
}
