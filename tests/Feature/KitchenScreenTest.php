<?php

use App\Livewire\Kitchen;
use App\Models\OrderNumbers;
use Livewire\Livewire;

function kitchenOrder(int $number, bool $ready = false, int $minutesAgo = 0, bool $byPhone = false): OrderNumbers
{
    $order = OrderNumbers::create(['number' => $number, 'is_ready' => $ready, 'by_phone' => $byPhone, 'is_taken' => false]);
    $order->forceFill(['created_at' => now()->subMinutes($minutesAgo)])->save();

    return $order;
}

it('lists preparing orders oldest first and ready orders newest first', function () {
    kitchenOrder(62, minutesAgo: 2);
    kitchenOrder(61, minutesAgo: 9);
    kitchenOrder(58, ready: true);
    kitchenOrder(59, ready: true)->forceFill(['updated_at' => now()->addSecond()])->save();

    $html = Livewire::test(Kitchen::class)
        ->assertSee('Gaminami')
        ->assertSee('Paruošti')
        ->html();

    $preparing = str($html)->between('data-kitchen-preparing', 'data-kitchen-ready')->toString();
    $ready = str($html)->after('data-kitchen-ready')->toString();

    expect(strpos($preparing, "makeOrderReady('61')"))->toBeLessThan(strpos($preparing, "makeOrderReady('62')"))
        ->and(strpos($ready, "makeOrderNotReady('59')"))->toBeLessThan(strpos($ready, "makeOrderNotReady('58')"));
});

it('colours the waiting time by how late the order is', function (int $minutesAgo, string $lateness) {
    kitchenOrder(70, minutesAgo: $minutesAgo);

    Livewire::test(Kitchen::class)
        ->assertSeeHtml("data-lateness=\"{$lateness}\"")
        ->assertSee("{$minutesAgo} min.");
})->with([
    'on time' => [5, 'on-time'],
    'late' => [Kitchen::LATE_AFTER_MINUTES, 'late'],
    'very late' => [Kitchen::VERY_LATE_AFTER_MINUTES + 3, 'very-late'],
]);

it('marks phone orders', function () {
    kitchenOrder(71, byPhone: true);

    Livewire::test(Kitchen::class)->assertSee('Telefonu');
});

it('moves an order to ready and back with a tap', function () {
    kitchenOrder(72);

    Livewire::test(Kitchen::class)
        ->call('makeOrderReady', '72')
        ->assertSeeHtml("makeOrderNotReady('72')")
        ->call('makeOrderNotReady', '72')
        ->assertSeeHtml("makeOrderReady('72')");

    expect(cache('pendingSounds'))->toContain(72);
});

it('shows empty states', function () {
    Livewire::test(Kitchen::class)
        ->assertSee('Visi užsakymai paruošti')
        ->assertSee('Paruoštų užsakymų nėra');
});
