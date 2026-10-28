<?php

use App\Livewire\Orders\OrdersList;
use Livewire\Volt\Volt;

it('prints for the client by default', function () {
    Volt::test('client-printer-settings')
        ->assertSet('enabled', true)
        ->assertSee('Spausdina kvitą klientui');
});

it('remembers that the client printer is turned off', function () {
    Volt::test('client-printer-settings')
        ->set('enabled', false)
        ->assertSee('Kvitas nespausdinamas');

    expect(cache(OrdersList::CLIENT_PRINTER_CACHE_KEY))->toBeFalse();

    Volt::test('client-printer-settings')
        ->assertSet('enabled', false);
});

it('points to the menu for the kitchen printing choice', function () {
    $this->get(route('settings'))
        ->assertOk()
        ->assertSee('Virtuvei')
        ->assertDontSee('Spausdinti tik su karštu maistu');
});
