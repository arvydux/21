<?php

namespace App\Livewire\Menu\Tables;

use Livewire\Attributes\On;
use Livewire\Component;

class PotatoToppingsTable extends Component
{
    public int $editedProductNameId = 0;

    public \App\Models\PotatoTopping $product;

    public string $name;

    public float $price;

    public string $sign;

    public ?float $package = null;

    public bool $show = false;

    public bool $is_default = false;

    public $products = [];

    public function editProduct(int $productNameId): void
    {
        $this->editedProductNameId = $productNameId;
        $this->product = \App\Models\PotatoTopping::find($productNameId);
        $this->name = $this->product->name;
        $this->price = $this->product->price;
        $this->sign = $this->product->sign;
        $this->show = $this->product->show;
        $this->is_default = $this->product->is_default;
        $this->package = $this->product->package;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'sign' => 'required',
        ]);

        if (is_null($this->product)) {
            $position = \App\Models\PotatoTopping::max('position') + 1;
            \App\Models\PotatoTopping::create(array_merge($this->only('name', 'price', 'show', 'is_default', 'sign', 'package'), ['position' => $position]));
        } else {
            $this->product->update($this->only('name', 'price', 'show', 'is_default', 'sign', 'package'));
        }

        $this->editedProductNameId = 0;
        $this->mount();
    }

    public function deleteProduct(int $productNameId): void
    {
        \App\Models\PotatoTopping::destroy($productNameId);
        $this->mount();
    }

    #[on('productAdded')]
    public function mount(): void
    {
        $this->products = \App\Models\PotatoTopping::all();
    }

    public function cancel(): void
    {
        $this->editedProductNameId = 0;
    }

    public function render()
    {
        return view('livewire.menu.tables.potato-toppings-table');
    }
}
