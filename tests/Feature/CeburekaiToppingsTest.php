<?php

use App\Livewire\Buttons\Ceburekai;
use App\Models\Category;
use App\Models\Topping;
use Database\Seeders\ToppingSeeder;
use Livewire\Livewire;

function toppingsMenu(): array
{
    return Topping::orderBy('position')->get()
        ->map(fn (Topping $topping) => [$topping->name, (float) $topping->price, $topping->sign])->all();
}

function expectedToppingsMenu(): array
{
    return [
        ['Grybai', 0.8, 'G'],
        ['Jelapenai', 0.8, 'J'],
        ['Saliamis', 0.8, 'SA'],
        ['Alyvuogės', 0.8, 'AL'],
        ['Mar. agurkai', 0.8, 'MA'],
        ['Sūris', 1.0, 'S'],
        ['Daugiau mėsos', 1.0, 'DM'],
        ['2-jų rūšių sūris', 1.2, '2S'],
    ];
}

it('seeds the toppings from the paper menu', function () {
    (new ToppingSeeder)->run();

    expect(toppingsMenu())->toBe(expectedToppingsMenu());
});

it('updates the original toppings, adds the missing ones and deletes the rest on an existing deployment', function () {
    Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai', 'position' => 0]);
    foreach ([
        ['name' => 'Sūriu', 'price' => 1.0, 'sign' => 'S'],
        ['name' => 'Grybai', 'price' => 0.6, 'sign' => 'G'],
        ['name' => 'Jelapenai', 'price' => 0.6, 'sign' => 'J'],
        ['name' => '2-jų rūšių sūris', 'price' => 1.2, 'sign' => '2S'],
        ['name' => 'Daugiau mėsos', 'price' => 4.0, 'sign' => '2S'],
        ['name' => 'Česnakas', 'price' => 0.5, 'sign' => 'C'],
    ] as $position => $topping) {
        Topping::create([...$topping, 'position' => 10 + $position]);
    }
    $cheeseId = Topping::where('name', 'Sūriu')->value('id');

    $migration = require database_path('migrations/2026_10_09_205055_update_ceburekai_toppings.php');
    $migration->up();
    $migration->up();

    expect(toppingsMenu())->toBe(expectedToppingsMenu())
        ->and(Topping::find($cheeseId)->name)->toBe('Sūris')
        ->and(Topping::where('name', 'Česnakas')->exists())->toBeFalse();
});

it('leaves a fresh database to the seeders', function () {
    $migration = require database_path('migrations/2026_10_09_205055_update_ceburekai_toppings.php');
    $migration->up();

    expect(Topping::count())->toBe(0);
});

it('offers the new toppings in the Čeburekai dialog', function () {
    (new ToppingSeeder)->run();

    Livewire::test(Ceburekai::class, ['showAll' => true])
        ->assertSeeInOrder(['Grybai', 'Saliamis', 'Alyvuogės', 'Mar. agurkai', 'Sūris', 'Daugiau mėsos']);
});
