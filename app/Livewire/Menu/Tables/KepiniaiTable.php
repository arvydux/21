<?php

namespace App\Livewire\Menu\Tables;

use Livewire\Attributes\On;
use Livewire\Component;

class KepiniaiTable extends Component
{
    public int $editedProductNameId = 0;

    public \App\Models\Kepiniai $simpleProduct;

    public ?int $number = null;

    public string $name;

    public float $price;

    public ?float $package = null;

    public bool $show = false;

    public ?int $subcategory_id = null;

    public $simpleProducts = [];

    public function editProduct(int $productNameId): void
    {
        $this->editedProductNameId = $productNameId;
        $this->simpleProduct = \App\Models\Kepiniai::find($productNameId);
        $this->number = $this->simpleProduct->number;
        $this->name = $this->simpleProduct->name;
        $this->price = $this->simpleProduct->price;
        $this->show = $this->simpleProduct->show;
        $this->package = $this->simpleProduct->package;
        $this->subcategory_id = $this->simpleProduct->subcategory_id;
    }

    public function save()
    {
        $this->validate([
            'number' => 'nullable|integer|min:1',
            'name' => 'required',
            'price' => 'required|numeric',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);

        if (is_null($this->simpleProduct)) {
            $position = \App\Models\Kepiniai::max('position') + 1;
            \App\Models\Kepiniai::create(array_merge($this->only('number', 'name', 'price', 'show', 'package', 'subcategory_id'), ['position' => $position]));
        } else {
            $this->simpleProduct->update($this->only('number', 'name', 'price', 'show', 'package', 'subcategory_id'));
        }

        $this->editedProductNameId = 0;
        $this->mount();
    }

    public function deleteProduct(int $productNameId): void
    {
        \App\Models\Kepiniai::destroy($productNameId);
        $this->mount();
    }

    #[on('productAdded')]
    public function mount(): void
    {
        $this->simpleProducts = \App\Models\Kepiniai::orderByRaw('number is null')->orderBy('number')->orderBy('position')->get();
    }

    public function cancel(): void
    {
        $this->editedProductNameId = 0;
    }

    #[On('subcategoryChanged')]
    public function refreshSubcategories(): void {}

    public function render()
    {
        return view('livewire.menu.tables.kepiniai-table', [
            'subcategories' => \App\Models\Subcategory::whereHas('category', fn ($q) => $q->where('component_name', 'kepiniai'))->orderBy('position')->get(),
        ]);
    }
}
