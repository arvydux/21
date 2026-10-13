<?php

use App\Livewire\KasaPanels;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

it('uses the default panel widths when none are saved', function () {
    Livewire::test(KasaPanels::class)
        ->assertSet('menuWidth', KasaPanels::MENU_DEFAULT)
        ->assertSet('cartWidth', KasaPanels::CART_DEFAULT)
        ->assertSeeHtml('data-panel-handle="menu"')
        ->assertSeeHtml('data-panel-handle="cart"');
});

it('saves the dragged panel widths for every cashier screen', function () {
    Livewire::test(KasaPanels::class)->call('saveWidths', 240, 420);

    expect(Cache::get(KasaPanels::CACHE_KEY))->toBe(['menu' => 240, 'cart' => 420]);

    Livewire::test(KasaPanels::class)
        ->assertSet('menuWidth', 240)
        ->assertSet('cartWidth', 420)
        ->assertSeeHtml('width: 240px;')
        ->assertSeeHtml('width: 420px;');
});

it('keeps saved widths within the allowed range', function (int $menu, int $cart, int $expectedMenu, int $expectedCart) {
    Livewire::test(KasaPanels::class)
        ->call('saveWidths', $menu, $cart)
        ->assertSet('menuWidth', $expectedMenu)
        ->assertSet('cartWidth', $expectedCart);
})->with([
    'too narrow' => [10, 50, KasaPanels::MENU_MIN, KasaPanels::CART_MIN],
    'too wide' => [9000, 9000, KasaPanels::MENU_MAX, KasaPanels::CART_MAX],
    'negative' => [-200, -1, KasaPanels::MENU_MIN, KasaPanels::CART_MIN],
]);

it('clamps widths that were stored out of range', function () {
    Cache::forever(KasaPanels::CACHE_KEY, ['menu' => 5, 'cart' => 5000]);

    Livewire::test(KasaPanels::class)
        ->assertSet('menuWidth', KasaPanels::MENU_MIN)
        ->assertSet('cartWidth', KasaPanels::CART_MAX);
});

it('renders the cashier page with the resizable panels', function () {
    $this->get(route('kasa'))
        ->assertSuccessful()
        ->assertSeeLivewire(KasaPanels::class);
});
