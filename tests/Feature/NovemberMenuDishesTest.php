<?php

use App\Livewire\Buttons\Ceburekai;
use App\Livewire\Buttons\OtherProducts;
use App\Models\Category;
use App\Models\Ceburek;
use App\Models\OtherProduct;
use App\Models\Subcategory;
use Livewire\Livewire;

beforeEach(function () {
    $ceburekai = Category::create(['name' => 'Čeburekai', 'component_name' => 'ceburekai', 'position' => 0]);
    $kiti = Category::create(['name' => 'Kiti', 'component_name' => 'other', 'position' => 3]);
    $this->cheese = Subcategory::create(['name' => 'Su sūriu', 'category_id' => $ceburekai->id, 'position' => 1]);
    $this->desserts = Subcategory::create(['name' => 'Desertai', 'category_id' => $kiti->id, 'position' => 2]);

    foreach ([[1, 'Sūris + grybai', 'SG'], [2, '2-jų rūšių sūris', '2S'], [3, 'Saliamis + sūris', 'SS']] as $position => [$number, $name, $sign]) {
        Ceburek::create(['number' => $number, 'name' => $name, 'price' => 5.50, 'sign' => $sign, 'show' => true, 'position' => $position, 'subcategory_id' => $this->cheese->id, 'for_kitchen' => true]);
    }

    foreach ([[1, 'Žagarėliai su ledais ir padažu', 5.50], [2, 'Ledai su vyšnių užpilu', 3.90], [3, 'Ledai su šokolado užpilu', 3.90], [4, 'Ledai su karamelės užpilu', 3.90]] as $position => [$number, $name, $price]) {
        OtherProduct::create(['number' => $number, 'name' => $name, 'price' => $price, 'show' => true, 'position' => $position, 'subcategory_id' => $this->desserts->id, 'for_kitchen' => true]);
    }
});

function runNovemberMenuMigration(): void
{
    (require database_path('migrations/2026_11_15_154323_add_cheese_mix_ceburekas_and_chocolate_zagareliai.php'))->up();
}

it('adds the cheese mix Čeburekas as number 4 of "Su sūriu"', function () {
    runNovemberMenuMigration();
    runNovemberMenuMigration();

    $mix = Ceburek::where('name', 'Sūrio MIX\'as su grybais')->sole();

    expect($mix->subcategory_id)->toBe($this->cheese->id)
        ->and($mix->number)->toBe(4)
        ->and((float) $mix->price)->toBe(6.9)
        ->and($mix->sign)->toBe('SM')
        ->and((bool) $mix->show)->toBeTrue()
        ->and(Ceburek::count())->toBe(4);
});

it('adds the chocolate and caramel Žagarėliai as number 5 of "Desertai"', function () {
    runNovemberMenuMigration();
    runNovemberMenuMigration();

    $zagareliai = OtherProduct::where('name', 'Žagarėliai su šokolado ir karamelės padažu')->sole();

    expect($zagareliai->subcategory_id)->toBe($this->desserts->id)
        ->and($zagareliai->number)->toBe(5)
        ->and((float) $zagareliai->price)->toBe(4.9)
        ->and((bool) $zagareliai->show)->toBeTrue()
        ->and(OtherProduct::count())->toBe(5);
});

it('leaves the till\'s own edits to the existing dishes alone', function () {
    Ceburek::where('name', 'Saliamis + sūris')->update(['price' => 5.80]);
    OtherProduct::where('name', 'Ledai su vyšnių užpilu')->update(['price' => 4.20, 'show' => false]);

    runNovemberMenuMigration();

    expect((float) Ceburek::where('name', 'Saliamis + sūris')->value('price'))->toBe(5.8)
        ->and((float) OtherProduct::where('name', 'Ledai su vyšnių užpilu')->value('price'))->toBe(4.2)
        ->and((bool) OtherProduct::where('name', 'Ledai su vyšnių užpilu')->value('show'))->toBeFalse();
});

it('adds the boiled cepelinai as snacks 8 and 9 and moves the snack platter to 10', function () {
    $snacks = Subcategory::create(['name' => 'Užkandžiai', 'category_id' => $this->desserts->category_id, 'position' => 3]);
    $koldunai = OtherProduct::create(['number' => 7, 'name' => 'Gruzdinti naminiai koldūnai su mėsa ir pikantišku padažu', 'price' => 6.50, 'show' => true, 'position' => 20, 'subcategory_id' => $snacks->id]);
    $platter = OtherProduct::create(['number' => 8, 'name' => 'Užkandžių rinkinys', 'price' => 19.00, 'show' => true, 'position' => 21, 'subcategory_id' => $snacks->id]);

    $migration = require database_path('migrations/2026_11_15_160225_add_boiled_cepelinai_to_snacks.php');
    $migration->up();
    $migration->up();

    expect(OtherProduct::where('subcategory_id', $snacks->id)->whereIn('number', [7, 8, 9, 10])->orderBy('position')->get()->map(fn (OtherProduct $product) => [$product->number, $product->name, (float) $product->price])->all())
        ->toContain([8, 'Virti naminiai cepelinai su grietine', 6.5])
        ->toContain([9, 'Virti naminiai cepelinai su spirgučiais', 6.9])
        ->and(OtherProduct::where('subcategory_id', $snacks->id)->orderBy('position')->get()->map(fn (OtherProduct $product) => $product->number)->filter(fn (?int $number) => in_array($number, [7, 8, 9, 10]))->values()->all())
        ->toBe([7, 8, 9, 10])
        ->and($platter->fresh()->number)->toBe(10)
        ->and((float) $platter->fresh()->price)->toBe(19.0)
        ->and($koldunai->fresh()->number)->toBe(7);
});

it('leaves a hand-renumbered snack platter alone', function () {
    $snacks = Subcategory::create(['name' => 'Užkandžiai', 'category_id' => $this->desserts->category_id, 'position' => 3]);
    $platter = OtherProduct::create(['number' => 12, 'name' => 'Užkandžių rinkinys', 'price' => 18.50, 'show' => true, 'position' => 5, 'subcategory_id' => $snacks->id]);

    (require database_path('migrations/2026_11_15_160225_add_boiled_cepelinai_to_snacks.php'))->up();

    expect($platter->fresh()->number)->toBe(12)
        ->and($platter->fresh()->position)->toBe(5);
});

it('puts the ice creams first in "Desertai" and moves the žagarėliai with ice cream to 4', function () {
    runNovemberMenuMigration();

    $migration = require database_path('migrations/2026_11_15_164722_renumber_desserts_ice_cream_first.php');
    $migration->up();
    $migration->up();

    expect(OtherProduct::where('subcategory_id', $this->desserts->id)->orderBy('position')->get()->map(fn (OtherProduct $product) => [$product->number, $product->name])->all())
        ->toBe([
            [1, 'Ledai su vyšnių užpilu'],
            [2, 'Ledai su šokolado užpilu'],
            [3, 'Ledai su karamelės užpilu'],
            [4, 'Žagarėliai su ledais ir padažu'],
            [5, 'Žagarėliai su šokolado ir karamelės padažu'],
        ]);
});

it('leaves a hand-renumbered dessert alone', function () {
    OtherProduct::where('name', 'Ledai su vyšnių užpilu')->update(['number' => 9]);

    (require database_path('migrations/2026_11_15_164722_renumber_desserts_ice_cream_first.php'))->up();

    expect(OtherProduct::where('name', 'Ledai su vyšnių užpilu')->value('number'))->toBe(9)
        ->and(OtherProduct::where('name', 'Žagarėliai su ledais ir padažu')->value('number'))->toBe(4);
});

it('sets the new Vištiena and Gurmaniškas prices', function () {
    $category = $this->cheese->category_id;
    $meat = Subcategory::create(['name' => 'Su mėsa', 'category_id' => $category, 'position' => 2]);
    $signature = Subcategory::create(['name' => 'Firminis', 'category_id' => $category, 'position' => 3]);
    $chicken = Ceburek::create(['number' => 2, 'name' => 'Vištiena', 'price' => 5.90, 'sign' => 'V', 'show' => true, 'position' => 10, 'subcategory_id' => $meat->id]);
    $gourmet = Ceburek::create(['number' => 2, 'name' => 'Gurmaniškas', 'price' => 7.20, 'sign' => 'G', 'show' => true, 'position' => 11, 'subcategory_id' => $signature->id]);
    $otherChicken = Ceburek::create(['name' => 'Vištiena', 'price' => 4.00, 'sign' => 'V', 'show' => true, 'position' => 12, 'subcategory_id' => $this->cheese->id]);

    (require database_path('migrations/2026_11_15_165740_update_vistiena_and_gurmaniskas_prices.php'))->up();

    expect((float) $chicken->fresh()->price)->toBe(6.5)
        ->and((float) $gourmet->fresh()->price)->toBe(7.5)
        ->and((float) $otherChicken->fresh()->price)->toBe(4.0);
});

it('shows the new dishes on the cashier screen', function () {
    runNovemberMenuMigration();

    Livewire::test(Ceburekai::class, ['subcategoryId' => $this->cheese->id])
        ->assertSeeInOrder(['Saliamis + sūris', '>4<', 'Sūrio MIX'], escape: false);

    Livewire::test(OtherProducts::class, ['subcategoryId' => $this->desserts->id])
        ->assertSeeInOrder(['Ledai su karamelės užpilu', '>5<', 'Žagarėliai su šokolado ir karamelės padažu'], escape: false);
});
