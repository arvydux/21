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

it('docks the ready numbers at the bottom of the middle panel and marks them taken on tap', function () {
    \App\Models\OrderNumbers::create(['number' => 58, 'is_ready' => true, 'by_phone' => false, 'is_taken' => false]);
    \App\Models\OrderNumbers::create(['number' => 59, 'is_ready' => true, 'by_phone' => true, 'is_taken' => false]);
    \App\Models\OrderNumbers::create(['number' => 60, 'is_ready' => false, 'by_phone' => false, 'is_taken' => false]);

    Livewire::test(ReadyOrders::class)
        ->assertSeeHtml('data-ready-strip')
        ->assertSee('Paruošti atsiimti')
        ->assertSeeHtml("makeOrderTaken('58')")
        ->assertSeeHtml("makeOrderTaken('59')")
        ->assertDontSeeHtml("makeOrderTaken('60')")
        ->assertDontSeeHtml('fixed bottom-0')
        ->call('makeOrderTaken', '58')
        ->assertDontSeeHtml("makeOrderTaken('58')");

    expect(\App\Models\OrderNumbers::where('number', 58)->value('is_taken'))->toBeTruthy();
});

it('hides the ready strip when nothing is ready', function () {
    Livewire::test(ReadyOrders::class)->assertDontSeeHtml('data-ready-strip');
});

it('renders the ready numbers inside the middle cashier panel', function () {
    $html = $this->get(route('kasa'))->assertOk()->getContent();

    expect($html)->toMatch('/<section[^>]*flex min-w-0 flex-1 flex-col[\s\S]*?kasa-category-section[\s\S]*?checkAndPlaySound[\s\S]*?<\/section>/');
});
