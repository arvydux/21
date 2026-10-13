<?php

use Livewire\Volt\Volt;

it('defaults to the Visi mode', function () {
    cache()->forget('subcategory_layout_mode');

    Volt::test('subcategory-layout-picker')
        ->assertSet('mode', 'visi_default');
});

it('persists the selected mode to cache', function () {
    Volt::test('subcategory-layout-picker')
        ->set('mode', 'persistent');

    expect(cache('subcategory_layout_mode'))->toBe('persistent');
});

it('loads the stored mode on mount', function () {
    cache()->forever('subcategory_layout_mode', 'persistent');

    Volt::test('subcategory-layout-picker')
        ->assertSet('mode', 'persistent');
});

it('persists the visi_default mode to cache', function () {
    Volt::test('subcategory-layout-picker')
        ->set('mode', 'visi_default');

    expect(cache('subcategory_layout_mode'))->toBe('visi_default');
});
