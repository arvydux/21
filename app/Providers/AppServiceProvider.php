<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Minishlink\WebPush\WebPush;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(WebPush::class, fn (): WebPush => new WebPush([
            'VAPID' => [
                'subject' => config('online-orders.vapid.subject'),
                'publicKey' => config('online-orders.vapid.public_key'),
                'privateKey' => config('online-orders.vapid.private_key'),
            ],
        ], timeout: 10));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
