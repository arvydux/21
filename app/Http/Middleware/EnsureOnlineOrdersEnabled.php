<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Closes the customers' order pages when order tracking is turned off in the settings,
 * so phones stop asking the till for updates.
 */
class EnsureOnlineOrdersEnabled
{
    public const CACHE_KEY = 'online_orders_enabled';

    public static function isEnabled(): bool
    {
        return (bool) cache(self::CACHE_KEY, true);
    }

    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(self::isEnabled(), 503);

        return $next($request);
    }
}
