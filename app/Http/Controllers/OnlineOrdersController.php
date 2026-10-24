<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyPushSubscriptionRequest;
use App\Http\Requests\StorePushSubscriptionRequest;
use App\Models\OrderNumbers;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
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
     * The personal page of one order, opened from the QR code on its receipt.
     */
    public function showOrder(string $token): Response
    {
        $order = OrderNumbers::query()->where('token', $token)->first();

        return response()->view('online-order', [
            'token' => $token,
            'number' => $order === null ? null : (int) $order->number,
            'status' => $order?->status() ?? 'missing',
        ], $order === null ? 404 : 200);
    }

    public function orderData(string $token): JsonResponse
    {
        $order = OrderNumbers::query()->where('token', $token)->first();

        return response()->json(
            $order === null ? ['status' => 'missing'] : ['number' => (int) $order->number, 'status' => $order->status()],
            $order === null ? 404 : 200,
        )->header('Cache-Control', 'no-store');
    }

    /**
     * Opens the personal page, not the general board, when the customer adds it to the home screen.
     */
    public function orderManifest(string $token): JsonResponse
    {
        return response()->json([
            'name' => 'Prusakovų užeiga — mano užsakymas',
            'short_name' => 'Užsakymas',
            'start_url' => route('online-orders.order', $token, absolute: false),
            'scope' => '/',
            'display' => 'standalone',
            'background_color' => '#FBF7F0',
            'theme_color' => '#14664A',
            'lang' => 'lt',
            'icons' => [
                ['src' => '/online-icon-192.png', 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => '/online-icon-512.png', 'sizes' => '512x512', 'type' => 'image/png'],
            ],
        ])->header('Content-Type', 'application/manifest+json');
    }

    /**
     * Remembers a browser that wants a push once its order is ready.
     * A browser follows one order at a time, so earlier requests from it are dropped.
     */
    public function subscribe(StorePushSubscriptionRequest $request): JsonResponse
    {
        $order = OrderNumbers::query()
            ->when(
                $request->filled('token'),
                fn ($query) => $query->where('token', $request->input('token')),
                fn ($query) => $query->where('number', $request->integer('number')),
            )
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
