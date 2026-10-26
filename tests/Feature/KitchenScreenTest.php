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

it('sums up what the preparing orders still need in the summary layout', function () {
    cache()->forever(Kitchen::LAYOUT_CACHE_KEY, Kitchen::LAYOUT_SUMMARY);
    $first = kitchenOrder(80);
    $first->kitchenItems()->createMany([
        ['name' => 'Kibinas su mėsa', 'amount' => 2, 'toppings' => ['Grietinė'], 'category' => 1],
        ['name' => 'Cepelinai', 'amount' => 1, 'category' => 5, 'comments' => 'Be spirgų'],
    ]);
    kitchenOrder(81)->kitchenItems()->create(['name' => 'Kibinas su mėsa', 'amount' => 3, 'toppings' => ['Grietinė', 'Česnakai'], 'category' => 1]);
    kitchenOrder(82, ready: true)->kitchenItems()->create(['name' => 'Čeburekas', 'amount' => 4, 'category' => 1]);

    $component = Livewire::test(Kitchen::class)
        ->assertSee('Gaminti')
        ->assertSee('Pastabos')
        ->assertSee('Cepelinai: Be spirgų')
        ->assertDontSee('Čeburekas')
        ->assertSeeHtml("makeOrderNotReady('82')");

    $preparing = OrderNumbers::where('is_ready', false)->with('kitchenItems')->orderBy('created_at')->get();

    expect($component->instance()->summary($preparing))->toBe([
        ['name' => 'Kibinas su mėsa', 'amount' => 5, 'toppings' => ['Grietinė' => 5, 'Česnakai' => 3]],
        ['name' => 'Cepelinai', 'amount' => 1, 'toppings' => []],
    ]);
});

it('keeps the ready column in the numbers layout', function () {
    kitchenOrder(83)->kitchenItems()->create(['name' => 'Cepelinai', 'amount' => 1]);

    Livewire::test(Kitchen::class)
        ->assertSee('Paruoštų užsakymų nėra')
        ->assertDontSeeHtml('data-kitchen-summary');
});

it('shows an empty summary when nothing is being made', function () {
    cache()->forever(Kitchen::LAYOUT_CACHE_KEY, Kitchen::LAYOUT_SUMMARY);

    Livewire::test(Kitchen::class)->assertSee('Nėra ką gaminti');
});
