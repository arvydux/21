<?php

use App\Livewire\Orders\OrdersList;
use App\Models\FreeNumbers;
use App\Models\Order;
use App\Models\OrderNumbers;
use App\Printing\TimedNetworkPrintConnector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;

beforeEach(function () {
    FreeNumbers::create(['number' => 7]);
});

function addCartLine(bool $forKitchen): void
{
    Order::create([
        'name' => json_encode([['Produktas' => 1.0]]),
        'toppings' => json_encode([]),
        'amount' => 1,
        'for_kitchen' => $forKitchen,
    ]);
}

/**
 * Starts a printer stand-in that takes every connection and reads it to the end.
 *
 * @return array{process: resource, port: int}
 */
function startFakePrinter(int $connections): array
{
    $script = <<<PHP
        \$server = stream_socket_server('tcp://127.0.0.1:0');
        echo parse_url('tcp://'.stream_socket_get_name(\$server, false), PHP_URL_PORT), PHP_EOL;
        for (\$i = 0; \$i < {$connections}; \$i++) {
            \$client = stream_socket_accept(\$server, 10);
            stream_get_contents(\$client);
            fclose(\$client);
        }
        PHP;

    $process = proc_open([PHP_BINARY, '-r', $script], [1 => ['pipe', 'w']], $pipes);

    return ['process' => $process, 'port' => (int) fgets($pipes[1])];
}

it('finishes the checkout and warns the cashier when both printers are unreachable', function () {
    Log::spy();
    addCartLine(true);

    $component = Livewire::test(OrdersList::class)
        ->call('makeOrder')
        ->assertDispatched('order-made')
        ->assertSet('unreachablePrinters', ['kitchen', 'client'])
        ->assertSee('Virtuvės ir kliento spausdintuvai nepasiekiami')
        ->assertSee('Spausdinti dar kartą');

    expect(OrderNumbers::where('number', 7)->exists())->toBeTrue()
        ->and(Order::count())->toBe(0)
        ->and(Cache::get($component->get('failedReceiptsKey')))->toHaveCount(3);

    Log::shouldHaveReceived('warning')->twice();
});

it('names only the client printer when nothing goes to the kitchen', function () {
    addCartLine(false);

    $component = Livewire::test(OrdersList::class)
        ->call('makeOrder')
        ->assertSet('unreachablePrinters', ['client'])
        ->assertSee('Kliento spausdintuvas nepasiekiamas');

    expect(Cache::get($component->get('failedReceiptsKey')))->toHaveCount(2);
});

it('shows no warning when every receipt printed', function () {
    $printer = startFakePrinter(2);
    config(['printers.client_ip' => '127.0.0.1', 'printers.port' => $printer['port']]);
    addCartLine(false);

    Livewire::test(OrdersList::class)
        ->call('makeOrder')
        ->assertSet('unreachablePrinters', [])
        ->assertSet('failedReceiptsKey', null)
        ->assertDontSee('spausdintuvas nepasiekiamas');

    proc_close($printer['process']);
});

it('keeps the warning when the retry fails again', function () {
    addCartLine(true);

    $component = Livewire::test(OrdersList::class)->call('makeOrder');
    $firstKey = $component->get('failedReceiptsKey');

    $component->call('retryFailedReceipts')
        ->assertSet('unreachablePrinters', ['kitchen', 'client'])
        ->assertSee('Virtuvės ir kliento spausdintuvai nepasiekiami');

    expect(Cache::get($component->get('failedReceiptsKey')))->toHaveCount(3)
        ->and(Cache::has($firstKey))->toBeFalse();
});

it('prints the failed receipts and hides the warning when the printer is back', function () {
    addCartLine(true);
    $component = Livewire::test(OrdersList::class)->call('makeOrder');
    $key = $component->get('failedReceiptsKey');

    $printer = startFakePrinter(3);
    config(['printers.port' => $printer['port']]);

    $component->call('retryFailedReceipts')
        ->assertSet('unreachablePrinters', [])
        ->assertSet('failedReceiptsKey', null)
        ->assertDontSee('spausdintuvai nepasiekiami');

    expect(Cache::has($key))->toBeFalse();

    proc_close($printer['process']);
});

it('sends receipts still waiting from earlier with the next checkout', function () {
    addCartLine(true);
    $component = Livewire::test(OrdersList::class)->call('makeOrder');

    addCartLine(false);
    $component->call('makeOrder');

    expect(Cache::get($component->get('failedReceiptsKey')))->toHaveCount(5);
});

it('forgets the failed receipts when the cashier closes the warning', function () {
    addCartLine(true);
    $component = Livewire::test(OrdersList::class)->call('makeOrder');
    $key = $component->get('failedReceiptsKey');

    $component->call('dismissPrinterWarning')
        ->assertSet('unreachablePrinters', [])
        ->assertSet('failedReceiptsKey', null)
        ->assertDontSee('spausdintuvai nepasiekiami');

    expect(Cache::has($key))->toBeFalse();
});

it('gives up quickly when a connected printer stops taking data', function () {
    $server = stream_socket_server('tcp://127.0.0.1:0');
    $port = (int) parse_url('tcp://'.stream_socket_get_name($server, false), PHP_URL_PORT);
    $connector = new TimedNetworkPrintConnector('127.0.0.1', $port, 1);
    $startedAt = microtime(true);

    expect(fn () => $connector->write(str_repeat('x', 16 * 1024 * 1024)))
        ->toThrow(Exception::class, 'Printer stopped taking data.')
        ->and(microtime(true) - $startedAt)->toBeLessThan(5);

    fclose($server);
});

it('prints only the kitchen receipt when the client printer is turned off', function () {
    cache()->forever(OrdersList::CLIENT_PRINTER_CACHE_KEY, false);
    addCartLine(true);

    $component = Livewire::test(OrdersList::class)
        ->call('makeOrder')
        ->assertSet('unreachablePrinters', ['kitchen'])
        ->assertSee('Virtuvės spausdintuvas nepasiekiamas');

    expect(Cache::get($component->get('failedReceiptsKey')))->toHaveCount(1);
});
