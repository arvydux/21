<?php

use App\Livewire\KasaMenu;
use App\Models\Category;
use Livewire\Livewire;

beforeEach(function () {
    Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai', 'position' => 0]);
    Category::create(['name' => 'Koldūnai bei bulvytės', 'component_name' => 'koldunai', 'position' => 1]);
    Category::create(['name' => 'Kiti', 'component_name' => 'other', 'position' => 2]);
});

it('lays the categories out in one row, or two even rows on narrow screens, in their saved order', function () {
    Livewire::test(KasaMenu::class)
        ->assertSeeHtml('--tabs-all: 3; --tabs-half: 2;')
        ->assertSeeInOrder(['Čeburekai', 'Koldūnai bei bulvytės', 'Kiti']);
});

it('highlights only the selected category', function () {
    $component = Livewire::test(KasaMenu::class)->call('categoryButtonClicked', 'koldunai', 2);

    expect(substr_count($component->html(), 'bg-white text-emerald-900'))->toBe(1)
        ->and($component->html())->toMatch('/bg-white text-emerald-900[^>]*>\s*<span[^>]*>Koldūnai bei bulvytės/');
});

it('tells the category section which category was chosen', function () {
    Livewire::test(KasaMenu::class)
        ->call('categoryButtonClicked', 'other', 3)
        ->assertSet('categoryMenuClicked', 'other')
        ->assertDispatched('category-button-clicked', buttonName: 'other', categoryId: 3);
});
