<?php

use App\Livewire\Buttons\Ceburekai;
use App\Livewire\Menu\CreateForm\CebureksCreateForm;
use App\Livewire\Menu\Tables\CebureksTable;
use App\Models\Category;
use App\Models\Ceburek;
use App\Models\Subcategory;
use Database\Seeders\CheeseCeburekSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->category = Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai', 'position' => 0]);
    $this->visi = Subcategory::create(['name' => 'Visi', 'category_id' => $this->category->id, 'position' => 0]);
});

function ceburekaiIn(Subcategory $subcategory): array
{
    return Ceburek::where('subcategory_id', $subcategory->id)->orderBy('position')->get()
        ->map(fn (Ceburek $ceburek) => [$ceburek->number, $ceburek->name, (float) $ceburek->price, $ceburek->sign])->all();
}

it('adds the cheese Čeburekai to a "Su sūriu" subcategory', function () {
    $migration = require database_path('migrations/2026_10_09_160752_add_cheese_ceburekai_subcategory.php');
    $migration->up();
    $migration->up();

    $subcategory = Subcategory::where('name', 'Su sūriu')->sole();

    expect($subcategory->category_id)->toBe($this->category->id)
        ->and($subcategory->position)->toBe(1)
        ->and(ceburekaiIn($subcategory))->toBe([
            [1, 'Sūris + grybai', 5.5, 'SG'],
            [2, '2-jų rūšių sūris', 5.5, '2S'],
            [3, 'Saliamis + sūris', 5.5, 'SS'],
        ]);
});

it('moves the existing cheese Čeburekai at the new price instead of duplicating them', function () {
    Ceburek::create(['name' => 'Sūris + grybai', 'price' => 4.50, 'sign' => 'SG', 'show' => true, 'position' => 3, 'subcategory_id' => $this->visi->id]);
    $meat = Ceburek::create(['name' => 'Čeburėkas su kiauliena', 'price' => 4.50, 'sign' => 'K', 'show' => true, 'position' => 4, 'subcategory_id' => $this->visi->id]);

    (new CheeseCeburekSeeder)->run();

    $subcategory = Subcategory::where('name', 'Su sūriu')->sole();
    expect(Ceburek::where('name', 'Sūris + grybai')->sole()->subcategory_id)->toBe($subcategory->id)
        ->and((float) Ceburek::where('name', 'Sūris + grybai')->value('price'))->toBe(5.5)
        ->and(Ceburek::count())->toBe(4)
        ->and($meat->fresh()->subcategory_id)->toBe($this->visi->id)
        ->and((float) $meat->fresh()->price)->toBe(4.5);
});

it('keeps admin edits to the cheese Čeburekai when run again', function () {
    (new CheeseCeburekSeeder)->run();
    Ceburek::where('name', 'Saliamis + sūris')->update(['price' => 5.80]);

    (new CheeseCeburekSeeder)->run();

    expect((float) Ceburek::where('name', 'Saliamis + sūris')->value('price'))->toBe(5.8)
        ->and(Ceburek::count())->toBe(3);
});

it('shows the cheese Čeburekai on the cashier screen', function () {
    (new CheeseCeburekSeeder)->run();

    Livewire::test(Ceburekai::class, ['subcategoryId' => Subcategory::where('name', 'Su sūriu')->value('id')])
        ->assertSeeInOrder(['Sūris + grybai', '2-jų rūšių sūris', 'Saliamis + sūris']);
});

it('adds the meat Čeburekai to a "Su mėsa" subcategory after "Su sūriu"', function () {
    (new CheeseCeburekSeeder)->run();

    $migration = require database_path('migrations/2026_10_09_160846_add_meat_ceburekai_subcategory.php');
    $migration->up();
    $migration->up();

    $subcategory = Subcategory::where('name', 'Su mėsa')->sole();

    expect($subcategory->position)->toBe(2)
        ->and(ceburekaiIn($subcategory))->toBe([
            [1, 'Kiauliena', 5.5, 'K'],
            [2, 'Vištiena', 5.9, 'V'],
            [3, 'Mix (kiaul. + jaut.)', 5.9, 'M'],
            [4, 'Jautiena', 6.5, 'J'],
            [5, 'Maišytas (jaut. + aviena)', 6.9, 'MA'],
            [6, 'Aviena', 7.2, 'A'],
            [7, 'Šefo', 7.5, 'Š'],
            [8, 'Gurmaniškas', 7.2, 'G'],
            [9, 'Pachmo', 7.5, 'P'],
        ]);
});

it('renames and moves the original meat Čeburekai instead of duplicating them', function () {
    $pork = Ceburek::create(['name' => 'Čeburėkas su kiauliena', 'price' => 4.50, 'sign' => 'K', 'show' => true, 'position' => 3, 'subcategory_id' => $this->visi->id]);
    $chef = Ceburek::create(['name' => 'Čeburėkas Šefo', 'price' => 6.80, 'sign' => 'Š', 'show' => true, 'position' => 9, 'subcategory_id' => $this->visi->id]);

    $migration = require database_path('migrations/2026_10_09_160846_add_meat_ceburekai_subcategory.php');
    $migration->up();

    $pork->refresh();
    expect($pork->name)->toBe('Kiauliena')
        ->and((float) $pork->price)->toBe(5.5)
        ->and($pork->subcategory->name)->toBe('Su mėsa')
        ->and(Ceburek::where('name', 'like', '%kiauliena%')->count())->toBe(1)
        ->and(Ceburek::count())->toBe(9)
        ->and($chef->fresh()->name)->toBe('Šefo')
        ->and((float) $chef->fresh()->price)->toBe(7.5)
        ->and($chef->fresh()->subcategory->name)->toBe('Su mėsa');
});

it('shows the menu number on the Čeburekai button', function () {
    (new CheeseCeburekSeeder)->run();

    Livewire::test(Ceburekai::class, ['subcategoryId' => Subcategory::where('name', 'Su sūriu')->value('id')])
        ->assertSeeInOrder(['>1<', 'Sūris + grybai', '>2<', '2-jų rūšių sūris', '>3<', 'Saliamis + sūris'], escape: false);
});

it('creates a Čeburekas with a menu number, or without one', function (mixed $number, ?int $expected) {
    Livewire::test(CebureksCreateForm::class)
        ->set('number', $number)
        ->set('name', 'Naujas čeburėkas')
        ->set('price', 6.00)
        ->set('subcategory_id', $this->visi->id)
        ->call('save')
        ->assertHasNoErrors();

    expect(Ceburek::where('name', 'Naujas čeburėkas')->value('number'))->toBe($expected);
})->with([
    'with number' => [4, 4],
    'empty' => ['', null],
]);

it('edits the menu number from the menu admin and rejects invalid ones', function () {
    $ceburek = Ceburek::create(['name' => 'Kiauliena', 'price' => 5.50, 'sign' => 'K', 'show' => true, 'position' => 0, 'subcategory_id' => $this->visi->id]);

    Livewire::test(CebureksTable::class)
        ->call('editProduct', $ceburek->id)
        ->set('number', 0)
        ->call('save')
        ->assertHasErrors(['number'])
        ->set('number', 5)
        ->call('save')
        ->assertHasNoErrors();

    expect($ceburek->fresh()->number)->toBe(5);
});
