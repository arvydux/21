<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps visitors coming in through the Cloudflare Tunnel on the online order board,
 * so the cashier, kitchen and menu admin stay reachable only from the local network.
 */
class RestrictPublicTunnel
{
    /** @var list<string> */
    private const PUBLIC_PATHS = ['online', 'online/data'];

    /** Personal order pages opened from the QR code on the receipt. */
    private const PUBLIC_ORDER_PATH = '#^online/(data/)?[A-Za-z0-9]{10}(/manifest\.webmanifest)?$#';

    /** @var list<string> */
    private const PUBLIC_POST_PATHS = ['online/subscribe', 'online/unsubscribe'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->cameThroughTunnel($request)) {
            return $next($request);
        }

        if ($request->isMethod('POST') && in_array($request->path(), self::PUBLIC_POST_PATHS, true)) {
            return $next($request);
        }

        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return response('Not Found', 404);
        }

        if ($request->path() === '/') {
            return response('', 302, ['Location' => '/online']);
        }

        if (! in_array($request->path(), self::PUBLIC_PATHS, true) && ! preg_match(self::PUBLIC_ORDER_PATH, $request->path())) {
            return response('Not Found', 404);
        }

        return $next($request);
    }

    private function cameThroughTunnel(Request $request): bool
    {
        return $request->getHost() === config('online-orders.public_host')
            || $request->headers->has('CF-Connecting-IP');
    }
}
