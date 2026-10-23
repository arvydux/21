<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Public Host
    |--------------------------------------------------------------------------
    |
    | The hostname the Cloudflare Tunnel exposes to the internet. Requests for
    | this host (or any request that arrived through Cloudflare) may only see
    | the online order board; every other page answers 404.
    |
    */

    'public_host' => env('ONLINE_ORDERS_HOST', 'status.prusakovuuzeiga.lt'),

    /*
    |--------------------------------------------------------------------------
    | Web Push (VAPID)
    |--------------------------------------------------------------------------
    |
    | Keys that sign the "order ready" push notifications. Generate a pair once
    | with Minishlink\WebPush\VAPID::createVapidKeys() and never change them,
    | or every saved browser subscription stops working.
    |
    */

    'vapid' => [
        'subject' => env('VAPID_SUBJECT', 'https://prusakovuuzeiga.lt'),
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
    ],

];
