<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyPushSubscriptionRequest;
use App\Http\Requests\StorePushSubscriptionRequest;
use App\Models\OrderNumbers;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class OnlineOrdersController extends Controller
{
    public function show(): View
    {
        return view('online-orders', $this->orders());
    }

    public function data(): JsonResponse
    {
        return response()->json($this->orders())->header('Cache-Control', 'no-store');
    }

    /**
     * Remembers a browser that wants a push once its order is ready.
     * A browser follows one order at a time, so earlier requests from it are dropped.
     */
    public function subscribe(StorePushSubscriptionRequest $request): JsonResponse
    {
        $order = OrderNumbers::query()
            ->where('number', $request->integer('number'))
            ->where('is_ready', false)
            ->where('is_taken', false)
            ->latest('id')
            ->first();

        if ($order === null) {
            return response()->json(['message' => 'Šis užsakymas jau paruoštas arba nerastas.'], 422);
        }

        PushSubscription::query()->where('endpoint', $request->input('endpoint'))->delete();

        $order->pushSubscriptions()->create([
            'endpoint' => $request->input('endpoint'),
            'public_key' => $request->input('keys.p256dh'),
            'auth_token' => $request->input('keys.auth'),
        ]);

        return response()->json(['subscribed' => true], 201);
    }

    public function unsubscribe(DestroyPushSubscriptionRequest $request): JsonResponse
    {
        PushSubscription::query()->where('endpoint', $request->input('endpoint'))->delete();

        return response()->json(['subscribed' => false]);
    }

    /**
     * @return array{preparing: list<int>, ready: list<int>}
     */
    private function orders(): array
    {
        return [
            'preparing' => OrderNumbers::query()
                ->where('is_ready', false)
                ->orderBy('created_at')
                ->pluck('number')
                ->map(fn ($number): int => (int) $number)
                ->all(),
            'ready' => OrderNumbers::query()
                ->where('is_ready', true)
                ->where('is_taken', false)
                ->orderByDesc('updated_at')
                ->pluck('number')
                ->map(fn ($number): int => (int) $number)
                ->all(),
        ];
    }
}
