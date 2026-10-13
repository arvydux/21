<?php

use App\Livewire\KasaMenu;
use App\Models\Category;
use Livewire\Livewire;

beforeEach(function () {
    Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai', 'position' => 0]);
    Category::create(['name' => 'Koldūnai bei bulvytės', 'component_name' => 'koldunai', 'position' => 1]);
    Category::create(['name' => 'Kiti', 'component_name' => 'other', 'position' => 2]);
});

it('lists the categories in a vertical sidebar, in their saved order', function () {
    Livewire::test(KasaMenu::class)
        ->assertSeeHtml('flex h-full flex-col gap-1.5')
        ->assertSeeInOrder(['Čeburekai', 'Koldūnai bei bulvytės', 'Kiti']);
});

it('marks only the selected category with the amber bar', function () {
    $component = Livewire::test(KasaMenu::class)->call('categoryButtonClicked', 'koldunai', 2);

    expect(substr_count($component->html(), 'data-selected-category-bar'))->toBe(1)
        ->and($component->html())->toMatch('/data-selected-category-bar[^>]*bg-amber-400(?:(?!data-sortable-id)[\s\S])*?>Koldūnai bei bulvytės</');
});

it('tells the category section which category was chosen', function () {
    Livewire::test(KasaMenu::class)
        ->call('categoryButtonClicked', 'other', 3)
        ->assertSet('categoryMenuClicked', 'other')
        ->assertDispatched('category-button-clicked', buttonName: 'other', categoryId: 3);
});
