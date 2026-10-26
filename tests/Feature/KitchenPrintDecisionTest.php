<?php

use App\Livewire\Buttons\Ceburekai;
use App\Livewire\Buttons\Drinks;
use App\Livewire\Buttons\Kepiniai as KepiniaiButtons;
use App\Livewire\Buttons\OtherProducts;
use App\Livewire\Menu\CreateForm\CebureksCreateForm;
use App\Livewire\Menu\CreateForm\DrinksCreateForm;
use App\Livewire\Menu\Tables\CebureksTable;
use App\Livewire\Menu\Tables\DrinksTable;
use App\Livewire\Orders\OrdersList;
use App\Models\Category;
use App\Models\Ceburek;
use App\Models\Drink;
use App\Models\Kepiniai;
use App\Models\Order;
use App\Models\OtherProduct;
use App\Models\Subcategory;
use Livewire\Livewire;

function makeKitchenTestOrder(string $name, bool $forKitchen): Order
{
    return Order::create([
        'name' => json_encode([[$name => 1.0]]),
        'amount' => 1,
        'for_kitchen' => $forKitchen,
    ]);
}

function shouldPrintForKitchen(): bool
{
    return Livewire::test(OrdersList::class)->instance()->shouldPrintForKitchen();
}

/**
 * @return list<string>
 */
function kitchenReceiptItemNames(): array
{
    $receipt = Livewire::test(OrdersList::class)->instance()->kitchenReceipt(1, false);
    $blocks = (fn () => $this->blocks)->call($receipt);

    return collect($blocks)
        ->where('type', 'line')
        ->where('amount', '!=', '')
        ->pluck('text')
        ->all();
}

function kitchenTestSubcategory(string $componentName): Subcategory
{
    $category = Category::create(['name' => $componentName, 'component_name' => $componentName, 'position' => 0]);

    return $category->subcategories()->first();
}

it('prints to kitchen when the cart has a kitchen line', function () {
    makeKitchenTestOrder('Čeburekas', true);

    expect(shouldPrintForKitchen())->toBeTrue();
});

it('prints to kitchen for a mixed cart', function () {
    makeKitchenTestOrder('Kava', false);
    makeKitchenTestOrder('Čeburekas', true);

    expect(shouldPrintForKitchen())->toBeTrue();
});

it('does not print to kitchen when no line is for the kitchen', function () {
    makeKitchenTestOrder('Kava', false);
    makeKitchenTestOrder('Kibinas', false);

    expect(shouldPrintForKitchen())->toBeFalse();
});

it('does not print to kitchen when the cart is empty', function () {
    expect(shouldPrintForKitchen())->toBeFalse();
});

it('puts only the kitchen lines on the kitchen receipt', function () {
    makeKitchenTestOrder('Kava', false);
    makeKitchenTestOrder('Čeburekas', true);
    makeKitchenTestOrder('Sriuba', true);

    expect(kitchenReceiptItemNames())->toBe(['Čeburekas', 'Sriuba']);
});

it('keeps the old kitchen choice as the default for new products', function () {
    $ceburek = Ceburek::create(['name' => 'Su kiauliena', 'price' => 5.5, 'sign' => 'A']);
    $otherProduct = OtherProduct::create(['name' => 'Sriuba', 'price' => 3.0]);
    $kepinys = Kepiniai::create(['name' => 'Kibinas', 'price' => 3.0]);
    $drink = Drink::create(['name' => 'Kava', 'price' => 2.0]);

    expect((bool) $ceburek->fresh()->for_kitchen)->toBeTrue()
        ->and((bool) $otherProduct->fresh()->for_kitchen)->toBeTrue()
        ->and((bool) $kepinys->fresh()->for_kitchen)->toBeFalse()
        ->and((bool) $drink->fresh()->for_kitchen)->toBeFalse();
});

it('marks a cart line by the kitchen setting of its product', function (string $button, string $model, bool $forKitchen) {
    $model::create(['name' => 'Produktas', 'price' => 2.0, 'show' => true, 'for_kitchen' => $forKitchen]);

    Livewire::test($button)->call('addProduct', 'Produktas', '2.00');

    expect((bool) Order::sole()->for_kitchen)->toBe($forKitchen);
})->with([
    'kepinys switched on' => [KepiniaiButtons::class, Kepiniai::class, true],
    'drink switched on' => [Drinks::class, Drink::class, true],
    'other product switched off' => [OtherProducts::class, OtherProduct::class, false],
]);

it('marks a ceburekas cart line by its kitchen setting', function (bool $forKitchen) {
    Ceburek::create(['name' => 'Su kiauliena', 'price' => 5.5, 'sign' => 'A', 'show' => true, 'for_kitchen' => $forKitchen]);

    Livewire::test(Ceburekai::class)
        ->set('productName', 'Su kiauliena')
        ->call('addOrder');

    expect((bool) Order::sole()->for_kitchen)->toBe($forKitchen);
})->with([true, false]);

it('changes the kitchen setting of one product in the menu table', function () {
    $drink = Drink::create(['name' => 'Kava', 'price' => 2.0, 'subcategory_id' => kitchenTestSubcategory('gerimai')->id]);

    Livewire::test(DrinksTable::class)
        ->call('editProduct', $drink->id)
        ->assertSet('for_kitchen', false)
        ->set('for_kitchen', true)
        ->call('save')
        ->assertHasNoErrors();

    expect((bool) $drink->fresh()->for_kitchen)->toBeTrue();
});

it('switches the kitchen setting for every product of the category at once', function (bool $forKitchen) {
    $first = Ceburek::create(['name' => 'Su kiauliena', 'price' => 5.5, 'sign' => 'A', 'for_kitchen' => ! $forKitchen]);
    $second = Ceburek::create(['name' => 'Su sūriu', 'price' => 5.5, 'sign' => 'A', 'for_kitchen' => ! $forKitchen]);

    Livewire::test(CebureksTable::class)
        ->assertSee('Spausdinti virtuvei')
        ->call('setKitchenForAll', $forKitchen);

    expect((bool) $first->fresh()->for_kitchen)->toBe($forKitchen)
        ->and((bool) $second->fresh()->for_kitchen)->toBe($forKitchen);
})->with([true, false]);

it('creates a product with the chosen kitchen setting', function () {
    $subcategory = kitchenTestSubcategory('gerimai');

    Livewire::test(DrinksCreateForm::class)
        ->assertSet('for_kitchen', false)
        ->set('name', 'Karštas šokoladas')
        ->set('price', '2,50')
        ->set('subcategory_id', $subcategory->id)
        ->set('for_kitchen', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('for_kitchen', false);

    expect((bool) Drink::sole()->for_kitchen)->toBeTrue();
});

it('suggests the kitchen for new čeburekai', function () {
    Livewire::test(CebureksCreateForm::class)->assertSet('for_kitchen', true);
});

it('shows the kitchen column in the menu table', function () {
    Drink::create(['name' => 'Kava', 'price' => 2.0, 'for_kitchen' => true]);

    Livewire::test(DrinksTable::class)->assertSeeInOrder(['Rodyti', 'Virtuvei']);
});
