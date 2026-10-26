<?php

use App\Livewire\Buttons\AlcoholicDrinks;
use App\Livewire\KasaCategorySection;
use App\Livewire\Menu\CreateForm\AlcoholicDrinksCreateForm;
use App\Livewire\Menu\Tables\AlcoholicDrinksTable;
use App\Livewire\Orders\OrdersList;
use App\Models\AlcoholicDrink;
use App\Models\Category;
use App\Models\Drink;
use App\Models\FreeNumbers;
use App\Models\Order;
use App\Models\Subcategory;
use Database\Seeders\DraftBeerSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->category = Category::create(['name' => 'Alkoholis', 'component_name' => 'alkoholis', 'position' => 0]);
    $this->visi = Subcategory::create(['name' => 'Visi', 'category_id' => $this->category->id, 'position' => 0]);
    $this->beer = AlcoholicDrink::factory()->create(['number' => 2, 'name' => 'Švyturys', 'price' => 3.50, 'subcategory_id' => $this->visi->id]);
});

it('shows the drinks with their menu numbers on the cashier screen', function () {
    AlcoholicDrink::factory()->create(['name' => 'Paslėptas', 'show' => false, 'subcategory_id' => $this->visi->id]);

    Livewire::test(AlcoholicDrinks::class, ['subcategoryId' => $this->visi->id])
        ->assertSeeInOrder(['>2<', 'Švyturys'], escape: false)
        ->assertDontSee('Paslėptas');
});

it('adds a drink to the cart with one click', function () {
    $this->beer->update(['package' => 0.10]);

    Livewire::test(AlcoholicDrinks::class)
        ->call('addProduct', 'Švyturys', '3.50')
        ->assertDispatched('change-order');

    $order = Order::sole();
    expect(json_decode($order->name, true))->toBe([['Švyturys' => '3.50']])
        ->and((float) $order->order_price)->toBe(3.5)
        ->and((float) $order->package)->toBe(0.1)
        ->and($order->category)->toBe(AlcoholicDrink::ORDER_CATEGORY);
});

it('increments the amount when the same drink is added again', function () {
    $component = Livewire::test(AlcoholicDrinks::class);
    $component->call('addProduct', 'Švyturys', '3.50');
    $component->call('addProduct', 'Švyturys', '3.50');

    expect(Order::sole()->amount)->toBe(2);
});

it('does not merge with a soft drink of the same name and price', function () {
    Order::create(['name' => json_encode([['Švyturys' => '3.50']]), 'order_price' => 3.5, 'amount' => 1, 'category' => 3]);

    Livewire::test(AlcoholicDrinks::class)->call('addProduct', 'Švyturys', '3.50');

    expect(Order::count())->toBe(2);
});

it('does not print alcohol on the kitchen ticket', function () {
    Livewire::test(AlcoholicDrinks::class)->call('addProduct', 'Švyturys', '3.50');

    expect(Livewire::test(OrdersList::class)->instance()->shouldPrintForKitchen())->toBeFalse();
});

it('decrements the stock when the order is made', function () {
    FreeNumbers::create(['number' => 1]);
    $this->beer->update(['left' => 5]);

    $component = Livewire::test(AlcoholicDrinks::class);
    $component->call('addProduct', 'Švyturys', '3.50');
    $component->call('addProduct', 'Švyturys', '3.50');

    ob_start();
    Livewire::test(OrdersList::class)->call('makeOrder');
    ob_end_clean();

    expect($this->beer->fresh()->left)->toBe(3)
        ->and(Order::count())->toBe(0);
});

it('creates a drink from the menu admin', function () {
    Livewire::test(AlcoholicDrinksCreateForm::class)
        ->set('number', 1)
        ->set('name', 'Vynas')
        ->set('price', 4.00)
        ->set('subcategory_id', $this->visi->id)
        ->call('save')
        ->assertHasNoErrors();

    $wine = AlcoholicDrink::where('name', 'Vynas')->sole();
    expect($wine->number)->toBe(1)
        ->and((float) $wine->price)->toBe(4.0)
        ->and(Drink::count())->toBe(0);
});

it('requires a name, price and subcategory for a new drink', function () {
    Livewire::test(AlcoholicDrinksCreateForm::class)
        ->call('save')
        ->assertHasErrors(['name', 'price', 'subcategory_id']);
});

it('only offers alcohol subcategories in the menu admin', function () {
    $drinks = Category::create(['name' => 'Gėrimai', 'component_name' => 'drinks', 'position' => 1]);
    Subcategory::create(['name' => 'Karšti gėrimai', 'category_id' => $drinks->id, 'position' => 0]);

    Livewire::test(AlcoholicDrinksCreateForm::class)
        ->assertSee('Visi')
        ->assertDontSee('Karšti gėrimai');
});

it('edits a drink from the menu admin', function () {
    Livewire::test(AlcoholicDrinksTable::class)
        ->assertSee('Švyturys')
        ->call('editProduct', $this->beer->id)
        ->set('price', 3.80)
        ->set('show', false)
        ->call('save')
        ->assertHasNoErrors();

    expect((float) $this->beer->fresh()->price)->toBe(3.8)
        ->and($this->beer->fresh()->show)->toBeFalsy();
});

it('shows the drinks when the category is opened on the cashier screen', function () {
    Livewire::test(KasaCategorySection::class)
        ->call('showProductMenu', 'alkoholis', $this->category->id)
        ->call('selectSubcategory', $this->visi->id)
        ->assertSeeLivewire(AlcoholicDrinks::class);
});

it('adds the empty category to an existing deployment', function () {
    $this->category->delete();
    Category::create(['name' => 'Gėrimai', 'component_name' => 'drinks', 'position' => 6]);

    $migration = require database_path('migrations/2026_10_09_130805_add_alkoholis_category.php');
    $migration->up();
    $migration->up();

    $category = Category::where('component_name', 'alkoholis')->sole();

    expect($category->name)->toBe('Alkoholis')
        ->and($category->position)->toBe(7)
        ->and(Subcategory::where('category_id', $category->id)->pluck('name')->all())->toBe(['Visi']);
});

it('leaves a fresh database to the seeders', function () {
    Category::query()->delete();

    $migration = require database_path('migrations/2026_10_09_130805_add_alkoholis_category.php');
    $migration->up();

    expect(Category::count())->toBe(0);
});

it('adds the draft beers to a "Pilstomas alus" subcategory', function () {
    $migration = require database_path('migrations/2026_10_09_145422_add_draft_beer_subcategory.php');
    $migration->up();

    $subcategory = Subcategory::where('name', 'Pilstomas alus')->sole();

    expect($subcategory->category_id)->toBe($this->category->id)
        ->and($subcategory->position)->toBe(1)
        ->and(AlcoholicDrink::where('subcategory_id', $subcategory->id)->orderBy('position')->get()->map(fn (AlcoholicDrink $drink) => [$drink->number, $drink->name, (float) $drink->price, (bool) $drink->show])->all())
        ->toBe([
            [1, '„Vilkmergės Premium lager“ (400 ml)', 4.4, true],
            [2, '„Vilkmergės“ tamsusis (400 ml)', 4.4, true],
        ])
        ->and($this->beer->fresh()->subcategory_id)->toBe($this->visi->id);
});

it('keeps admin edits to the draft beers when run again', function () {
    (new DraftBeerSeeder)->run();
    AlcoholicDrink::where('name', '„Vilkmergės“ tamsusis (400 ml)')->update(['price' => 4.60]);

    (new DraftBeerSeeder)->run();

    expect(AlcoholicDrink::count())->toBe(3)
        ->and(Subcategory::where('name', 'Pilstomas alus')->count())->toBe(1)
        ->and((float) AlcoholicDrink::where('name', '„Vilkmergės“ tamsusis (400 ml)')->value('price'))->toBe(4.6);
});

it('moves an unnumbered draft beer from another subcategory instead of duplicating it', function () {
    AlcoholicDrink::factory()->create(['name' => '„Vilkmergės“ tamsusis (400 ml)', 'price' => 4.00, 'subcategory_id' => $this->visi->id]);

    (new DraftBeerSeeder)->run();

    $darkBeer = AlcoholicDrink::where('name', '„Vilkmergės“ tamsusis (400 ml)')->sole();
    expect($darkBeer->subcategory->name)->toBe('Pilstomas alus')
        ->and((float) $darkBeer->price)->toBe(4.4);
});

it('adds a draft beer to the cart with one click', function () {
    (new DraftBeerSeeder)->run();

    Livewire::test(AlcoholicDrinks::class, ['subcategoryId' => Subcategory::where('name', 'Pilstomas alus')->value('id')])
        ->assertSee('„Vilkmergės Premium lager“ (400 ml)')
        ->call('addProduct', '„Vilkmergės Premium lager“ (400 ml)', '4.40');

    expect(json_decode(Order::sole()->name, true))->toBe([['„Vilkmergės Premium lager“ (400 ml)' => '4.40']]);
});

it('adds the cider to a "Sidras" subcategory after the draft beers', function () {
    (new DraftBeerSeeder)->run();

    $migration = require database_path('migrations/2026_10_09_150108_add_cider_subcategory.php');
    $migration->up();
    $migration->up();

    $subcategory = Subcategory::where('name', 'Sidras')->sole();

    expect($subcategory->category_id)->toBe($this->category->id)
        ->and($subcategory->position)->toBe(2)
        ->and(AlcoholicDrink::where('subcategory_id', $subcategory->id)->get()->map(fn (AlcoholicDrink $drink) => [$drink->number, $drink->name, (float) $drink->price])->all())
        ->toBe([[1, 'Vilkmergės CRAFT sidras (400 ml)', 4.6]]);
});

it('adds the wines to a "Vynas" subcategory', function () {
    $migration = require database_path('migrations/2026_10_09_150350_add_wine_subcategory.php');
    $migration->up();
    $migration->up();

    $subcategory = Subcategory::where('name', 'Vynas')->sole();

    expect($subcategory->category_id)->toBe($this->category->id)
        ->and(AlcoholicDrink::where('subcategory_id', $subcategory->id)->orderBy('number')->get()->map(fn (AlcoholicDrink $drink) => [$drink->number, $drink->name, (float) $drink->price])->all())
        ->toBe([
            [1, 'Baltasis sausas vynas „COLPASSO“ (150 ml)', 5.5],
            [2, 'Baltasis sausas vynas „COLPASSO“ (750 ml)', 22.0],
            [3, 'Raudonasis sausas vynas „PRECISO“ (150 ml)', 5.7],
            [4, 'Raudonasis sausas vynas „PRECISO“ (750 ml)', 24.0],
            [5, 'Raudonas saldus vynas „KAGOR“ (150 ml)', 5.2],
            [6, 'Raudonas saldus vynas „KAGOR“ (750 ml)', 20.0],
            [7, 'Putojantis vynas „Escanza PROSECCO“ (150 ml)', 5.5],
            [8, 'Putojantis vynas „Escanza PROSECCO“ (750 ml)', 22.0],
            [9, 'Putojantis saldus vynas „BOSCA“ (150 ml)', 3.9],
            [10, 'Putojantis saldus vynas „BOSCA“ (750 ml)', 13.0],
            [11, 'Karštas vynas (150 ml)', 5.4],
        ]);
});

it('adds the spirits to a "Stiprieji alkoholiniai gėrimai" subcategory', function () {
    $migration = require database_path('migrations/2026_10_09_151501_add_spirits_subcategory.php');
    $migration->up();
    $migration->up();

    $subcategory = Subcategory::where('name', 'Stiprieji alkoholiniai gėrimai')->sole();

    expect($subcategory->category_id)->toBe($this->category->id)
        ->and(AlcoholicDrink::where('subcategory_id', $subcategory->id)->orderBy('number')->get()->map(fn (AlcoholicDrink $drink) => [$drink->number, $drink->name, (float) $drink->price])->all())
        ->toBe([
            [1, 'Degtinė LITHUANIAN AUKSINĖ (40 ml)', 3.5],
            [2, 'Degtinė LITHUANIAN AUKSINĖ (500 ml)', 30.0],
            [3, 'Viskis JACK DANIEL’S (40 ml)', 4.6],
            [4, 'Viskis JACK DANIEL’S (500 ml)', 40.0],
        ]);
});

it('passes names with quotes to the cart button safely', function () {
    $this->beer->update(['name' => "Viskis JACK DANIEL'S"]);

    Livewire::test(AlcoholicDrinks::class, ['subcategoryId' => $this->visi->id])
        ->assertSeeHtml("addProduct('Viskis JACK DANIEL\\u0027S'")
        ->assertDontSeeHtml("DANIEL'S'");
});
