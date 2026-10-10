<?php

use App\Livewire\Buttons\Ceburekai;
use App\Livewire\Buttons\Drinks;
use App\Livewire\Buttons\Kepiniai;
use App\Livewire\Buttons\OtherProducts;
use App\Models\Ceburek;
use App\Models\Drink;
use App\Models\Kepiniai as KepiniaiModel;
use App\Models\OtherProduct;
use App\Models\Topping;
use Livewire\Livewire;

it('passes product names with quotes to the button handlers safely', function (string $component, string $model, array $attributes) {
    $model::create(['name' => "Jack Daniel's", 'price' => 4.60, 'show' => true, 'position' => 0, ...$attributes]);

    Livewire::test($component, ['showAll' => true])
        ->assertSeeHtml("'Jack Daniel\\u0027s'")
        ->assertDontSeeHtml("Daniel's'")
        ->assertDontSeeHtml('Daniel&#039;s&#039;');
})->with([
    'Gėrimai' => [Drinks::class, Drink::class, []],
    'Kepiniai' => [Kepiniai::class, KepiniaiModel::class, []],
    'Kiti' => [OtherProducts::class, OtherProduct::class, []],
    'Čeburekai' => [Ceburekai::class, Ceburek::class, ['sign' => 'JD']],
]);

it('passes Čeburekai topping names with quotes to the edit handler safely', function () {
    Topping::create(['name' => "Chef's sauce", 'price' => 0.60, 'sign' => 'CS', 'show' => true, 'position' => 0]);

    Livewire::test(Ceburekai::class, ['showAll' => true])
        ->assertSeeHtml("'Chef\\u0027s sauce'")
        ->assertDontSeeHtml('Chef&#039;s sauce&#039;');
});
