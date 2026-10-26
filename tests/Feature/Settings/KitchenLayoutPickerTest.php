<?php

use App\Livewire\Kitchen;
use Livewire\Volt\Volt;

it('defaults to the numbers layout', function () {
    Volt::test('kitchen-layout-picker')
        ->assertSet('layout', Kitchen::LAYOUT_NUMBERS)
        ->assertSee('Numeriai')
        ->assertSee('Suvestinė');
});

it('remembers the chosen layout', function () {
    Volt::test('kitchen-layout-picker')
        ->set('layout', Kitchen::LAYOUT_SUMMARY);

    expect(cache(Kitchen::LAYOUT_CACHE_KEY))->toBe(Kitchen::LAYOUT_SUMMARY);

    Volt::test('kitchen-layout-picker')
        ->assertSet('layout', Kitchen::LAYOUT_SUMMARY);
});

it('ignores an unknown layout', function () {
    Volt::test('kitchen-layout-picker')
        ->set('layout', 'nonsense')
        ->assertSet('layout', Kitchen::LAYOUT_NUMBERS);

    expect(cache(Kitchen::LAYOUT_CACHE_KEY))->toBeNull();
});
