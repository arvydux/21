<?php

use App\Livewire\Orders\OrdersList;
use App\Livewire\ReadyOrders;
use Livewire\Livewire;

it('shows the sound activation button until it is pressed', function () {
    Livewire::test(ReadyOrders::class)
        ->assertSee('Aktyvuoti garsą')
        ->assertSeeHtml('x-teleport="#sound-activation-slot"')
        ->call('enableSound')
        ->assertSet('soundEnabled', true)
        ->assertDontSee('Aktyvuoti garsą');
});

it('gives the sound button a place in the cart header', function () {
    Livewire::test(OrdersList::class)
        ->assertSeeHtml('id="sound-activation-slot" wire:ignore');
});
