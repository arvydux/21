<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Receipt Printers
    |--------------------------------------------------------------------------
    |
    | Network addresses of the thermal printers the till prints to, and how
    | many seconds to wait for one before telling the cashier it's unreachable.
    |
    */

    'kitchen_ip' => env('KITCHEN_PRINTER_IP', '192.168.32.100'),

    'client_ip' => env('CLIENT_PRINTER_IP', '192.168.32.101'),

    'port' => (int) env('PRINTER_PORT', 9100),

    'timeout' => (int) env('PRINTER_TIMEOUT', 2),

];
