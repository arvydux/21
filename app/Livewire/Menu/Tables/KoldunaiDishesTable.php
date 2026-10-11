<?php

namespace App\Livewire\Menu\Tables;

use Livewire\Attributes\On;
use Livewire\Component;

class KoldunaiDishesTable extends Component
{
    public int $editedProductNameId = 0;

    public \App\Models\KoldunaiDish $product;

    public ?int $number = null;

    public string $name;

    public float $price;

    public ?float $package = null;

    public string $sign;

    public bool $show = false;

    public ?int $subcategory_id = null;

    public $products = [];

    public function editProduct(int $productNameId): void
    {
        $this->editedProductNameId = $productNameId;
        $this->product = \App\Models\KoldunaiDish::find($productNameId);
        $this->number = $this->product->number;
        $this->name = $this->product->name;
        $this->price = $this->product->price;
        $this->sign = $this->product->sign;
        $this->show = $this->product->show;
        $this->package = $this->product->package;
        $this->subcategory_id = $this->product->subcategory_id;
    }

    public function save()
    {
        $this->validate([
            'number' => 'nullable|integer|min:1',
            'name' => 'required',
            'price' => 'required|numeric',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);

        if (is_null($this->product)) {
            $position = \App\Models\KoldunaiDish::max('position') + 1;
            \App\Models\KoldunaiDish::create(array_merge($this->only('number', 'name', 'price', 'show', 'sign', 'package', 'subcategory_id'), ['position' => $position]));
        } else {
            $this->product->update($this->only('number', 'name', 'price', 'show', 'sign', 'package', 'subcategory_id'));
        }

        $this->editedProductNameId = 0;
        $this->mount();
    }

    public function deleteProduct(int $productNameId): void
    {
        \App\Models\KoldunaiDish::destroy($productNameId);
        $this->mount();
    }

    #[on('productAdded')]
    public function mount(): void
    {
        $this->products = \App\Models\KoldunaiDish::orderByRaw('number is null')->orderBy('number')->orderBy('position')->get();
    }

    public function cancel(): void
    {
        $this->editedProductNameId = 0;
    }

    #[On('subcategoryChanged')]
    public function refreshSubcategories(): void {}

    public function render()
    {
        return view('livewire.menu.tables.koldunai-dishes-table', [
            'subcategories' => \App\Models\Subcategory::whereHas('category', fn ($q) => $q->where('component_name', 'koldunai'))->orderBy('position')->get(),
        ]);
    }
}
