<?php

use Livewire\Volt\Volt;

it('defaults to drilldown mode', function () {
    cache()->forget('subcategory_layout_mode');

    Volt::test('subcategory-layout-picker')
        ->assertSet('mode', 'drilldown');
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
