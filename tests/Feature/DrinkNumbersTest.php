<?php

use App\Livewire\Buttons\Drinks;
use App\Livewire\Menu\CreateForm\DrinksCreateForm;
use App\Livewire\Menu\Tables\DrinksTable;
use App\Models\Category;
use App\Models\Drink;
use App\Models\Subcategory;
use Database\Seeders\ColdDrinkSeeder;
use Database\Seeders\HotDrinkSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->category = Category::create(['name' => 'Gėrimai', 'component_name' => 'drinks', 'position' => 0]);
    $this->visi = Subcategory::create(['name' => 'Visi', 'category_id' => $this->category->id, 'position' => 0]);
    $this->drink = Drink::create(['name' => 'Pepsi', 'price' => 2.0, 'show' => true, 'position' => 0, 'subcategory_id' => $this->visi->id]);
});

it('adds the numbered hot drinks to a "Karšti gėrimai" subcategory', function () {
    (new HotDrinkSeeder)->run();

    $subcategory = Subcategory::where('name', 'Karšti gėrimai')->sole();

    expect($subcategory->category_id)->toBe($this->category->id)
        ->and($subcategory->position)->toBe(1)
        ->and(Drink::where('subcategory_id', $subcategory->id)->orderBy('number')->get(['number', 'name', 'price'])->map(fn (Drink $drink) => [$drink->number, $drink->name, (float) $drink->price])->all())
        ->toBe([
            [1, 'Juoda/žalia/vaisinė arbata iš pakelio', 1.2],
            [2, 'Plikyta arbata juoda/žalia/vaisinė', 3.5],
            [3, 'Pasiflorų arbata', 4.5],
            [4, 'Aviečių arbata', 4.5],
            [5, 'Šaltalankių-imbierų arbata', 4.5],
            [6, 'Kava juoda', 2.2],
            [7, 'Kava Espresso', 2.2],
            [8, 'Kava Latte', 2.5],
            [9, 'Kava Cappuccino', 2.5],
            [10, 'Kava be kofeino', 2.5],
        ])
        ->and($this->drink->fresh()->number)->toBeNull();
});

it('adds the numbered cold drinks to a "Šalti gėrimai" subcategory after the hot drinks', function () {
    (new HotDrinkSeeder)->run();
    (new ColdDrinkSeeder)->run();

    $subcategory = Subcategory::where('name', 'Šalti gėrimai')->sole();

    expect($subcategory->category_id)->toBe($this->category->id)
        ->and($subcategory->position)->toBe(2)
        ->and(Drink::where('subcategory_id', $subcategory->id)->orderBy('number')->get(['number', 'name', 'price'])->map(fn (Drink $drink) => [$drink->number, $drink->name, (float) $drink->price])->all())
        ->toBe(coldDrinksMenu())
        ->and($this->drink->fresh()->subcategory_id)->toBe($this->visi->id);
});

it('keeps admin edits to a numbered drink when run again', function () {
    (new ColdDrinkSeeder)->run();
    Drink::where('name', 'Pepsi (330 ml)')->update(['price' => 2.00, 'number' => 20]);

    (new ColdDrinkSeeder)->run();

    $pepsi = Drink::where('name', 'Pepsi (330 ml)')->sole();
    expect((float) $pepsi->price)->toBe(2.0)
        ->and($pepsi->number)->toBe(20);
});

it('replaces every drink in "Šalti gėrimai" and leaves other subcategories alone', function () {
    $coldDrinks = Subcategory::create(['name' => 'Šalti gėrimai', 'category_id' => $this->category->id, 'position' => 1]);
    Drink::create(['name' => 'Pepsi, Mirinda, 7UP', 'number' => 1, 'price' => 1.8, 'show' => true, 'position' => 1, 'subcategory_id' => $coldDrinks->id]);
    Drink::create(['name' => 'Tauras gira', 'number' => 2, 'price' => 2.2, 'show' => true, 'position' => 2, 'subcategory_id' => $coldDrinks->id]);

    $migration = require database_path('migrations/2026_10_09_152745_replace_cold_drinks.php');
    $migration->up();
    $migration->up();

    expect(Subcategory::where('name', 'Šalti gėrimai')->count())->toBe(1)
        ->and(Drink::where('subcategory_id', $coldDrinks->id)->orderBy('number')->get()->map(fn (Drink $drink) => [$drink->number, $drink->name, (float) $drink->price])->all())
        ->toBe(coldDrinksMenu())
        ->and(Drink::whereIn('name', ['Pepsi, Mirinda, 7UP', 'Tauras gira'])->exists())->toBeFalse()
        ->and($this->drink->fresh())->not->toBeNull();
});

it('skips replacing cold drinks on a fresh database', function () {
    $this->category->delete();

    $migration = require database_path('migrations/2026_10_09_152745_replace_cold_drinks.php');
    $migration->up();

    expect(Drink::count())->toBe(1);
});

/**
 * @return list<array{int, string, float}>
 */
function coldDrinksMenu(): array
{
    return [
        [1, 'Apelsinų sultys', 2.5],
        [2, 'Obuolių sultys', 2.5],
        [3, 'Pomidorų sultys', 2.5],
        [4, 'Pepsi (330 ml)', 1.8],
        [5, '7UP (330 ml)', 1.8],
        [6, 'Mirinda (330 ml)', 1.8],
        [7, 'Gira „Vilkmergės“ (400 ml)', 2.8],
        [8, 'Pilstoma gira „TAURAS“ (500 ml)', 3.3],
        [9, 'Gazuotas vanduo (500 ml)', 1.2],
        [10, 'Negazuotas vanduo (500 ml)', 1.2],
        [11, 'Energetinis gėrimas CULT (N18)', 2.0],
        [12, 'Nealkoholinis alus „Vilkmergės“ (N18)', 3.6],
    ];
}

it('does not duplicate hot drinks when run again', function () {
    (new HotDrinkSeeder)->run();
    (new HotDrinkSeeder)->run();

    expect(Subcategory::where('name', 'Karšti gėrimai')->count())->toBe(1)
        ->and(Drink::count())->toBe(11);
});

it('skips hot drinks when there is no drinks category', function () {
    $this->category->delete();

    (new HotDrinkSeeder)->run();

    expect(Drink::count())->toBe(1);
});

it('shows the menu number on the cashier button', function () {
    $this->drink->update(['number' => 7]);

    Livewire::test(Drinks::class, ['showAll' => true])
        ->assertSeeInOrder(['>7<', 'Pepsi'], escape: false);
});

it('saves the menu number from the menu admin', function () {
    Livewire::test(DrinksTable::class)
        ->call('editProduct', $this->drink->id)
        ->set('number', 3)
        ->call('save')
        ->assertHasNoErrors();

    expect($this->drink->fresh()->number)->toBe(3);
});

it('rejects a menu number that is not a positive whole number', function (mixed $number) {
    Livewire::test(DrinksTable::class)
        ->call('editProduct', $this->drink->id)
        ->set('number', $number)
        ->call('save')
        ->assertHasErrors(['number']);
})->with([
    'zero' => 0,
    'negative' => -1,
]);

it('creates a drink with a menu number, or without one', function (mixed $number, ?int $expected) {
    Livewire::test(DrinksCreateForm::class)
        ->set('number', $number)
        ->set('name', 'Naujas gėrimas')
        ->set('price', 2.00)
        ->set('subcategory_id', $this->visi->id)
        ->call('save')
        ->assertHasNoErrors();

    expect(Drink::where('name', 'Naujas gėrimas')->value('number'))->toBe($expected);
})->with([
    'with number' => [8, 8],
    'empty' => ['', null],
]);

it('lists numbered drinks first in the menu admin', function () {
    Drink::create(['name' => 'Kava', 'number' => 2, 'price' => 2.2, 'show' => true, 'position' => 5, 'subcategory_id' => $this->visi->id]);
    Drink::create(['name' => 'Arbata', 'number' => 1, 'price' => 1.2, 'show' => true, 'position' => 6, 'subcategory_id' => $this->visi->id]);

    Livewire::test(DrinksTable::class)
        ->assertSeeInOrder(['Arbata', 'Kava', 'Pepsi']);
});
