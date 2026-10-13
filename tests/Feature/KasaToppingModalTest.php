<?php

use App\Livewire\Buttons\Ceburekai;
use App\Livewire\Buttons\KoldunaiDishes;
use App\Livewire\Buttons\PotatoDishes;
use App\Models\Topping;

it('shows the toppings popup of every dish in the cashier design', function (string $component, string $modalName) {
    Livewire\Livewire::test($component, ['showAll' => true])
        ->assertSeeHtml('data-topping-modal')
        ->assertSeeHtml("data-modal=\"{$modalName}\"")
        ->assertSeeHtml('x-text="chosenName"')
        ->assertSee('Priedai')
        ->assertSee('Kur valgys?')
        ->assertSee('Pridėti dar vieną')
        ->assertSee('Pridėti ir uždaryti')
        ->assertDontSeeHtml('bg-white!');
})->with([
    'čeburekai' => [Ceburekai::class, 'choose-toppings'],
    'potato dishes' => [PotatoDishes::class, 'choose-potato-toppings'],
    'koldūnai' => [KoldunaiDishes::class, 'choose-koldunai-toppings'],
]);

it('shows each topping with its price', function () {
    Topping::create(['name' => 'Grybai', 'price' => 0.8, 'sign' => 'G', 'show' => true, 'position' => 0]);

    Livewire\Livewire::test(Ceburekai::class, ['showAll' => true])
        ->assertSeeHtml('id="ceburek-topping-')
        ->assertSee('Grybai')
        ->assertSee('+0,80 €');
});
