<?php

namespace App\Livewire\Menu\Tables;

use Livewire\Attributes\On;
use Livewire\Component;

class PotatoDishesTable extends Component
{
    public int $editedProductNameId = 0;

    public \App\Models\PotatoDish $product;

    public string $name;

    public float $price;

    public float $half_price;

    public ?float $package = null;

    public string $sign;

    public bool $show = false;

    public ?int $subcategory_id = null;

    public $products = [];

    public function editProduct(int $productNameId): void
    {
        $this->editedProductNameId = $productNameId;
        $this->product = \App\Models\PotatoDish::find($productNameId);
        $this->name = $this->product->name;
        $this->price = $this->product->price;
        $this->half_price = $this->product->half_price;
        $this->sign = $this->product->sign;
        $this->show = $this->product->show;
        $this->package = $this->product->package;
        $this->subcategory_id = $this->product->subcategory_id;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'half_price' => 'required|numeric',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);

        if (is_null($this->product)) {
            $position = \App\Models\PotatoDish::max('position') + 1;
            \App\Models\PotatoDish::create(array_merge($this->only('name', 'price', 'half_price', 'show', 'sign', 'package', 'subcategory_id'), ['position' => $position]));
        } else {
            $this->product->update($this->only('name', 'price', 'half_price', 'show', 'sign', 'package', 'subcategory_id'));
        }

        $this->editedProductNameId = 0;
        $this->mount();
    }

    public function deleteProduct(int $productNameId): void
    {
        \App\Models\PotatoDish::destroy($productNameId);
        $this->mount();
    }

    #[on('productAdded')]
    public function mount(): void
    {
        $this->products = \App\Models\PotatoDish::all();
    }

    public function cancel(): void
    {
        $this->editedProductNameId = 0;
    }

    #[On('subcategoryChanged')]
    public function refreshSubcategories(): void {}

    public function render()
    {
        return view('livewire.menu.tables.potato-dishes-table', [
            'subcategories' => \App\Models\Subcategory::whereHas('category', fn ($q) => $q->where('component_name', 'bulviniai'))->orderBy('position')->get(),
        ]);
    }
}
