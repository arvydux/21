<?php

namespace App\Livewire\Menu\Tables;

use Livewire\Attributes\On;
use Livewire\Component;

class ToppingsTable extends Component
{
    public int $editedProductNameId = 0;
    public \App\Models\Topping $product;
    public string $name;
    public string $price;
    public string $sign;
    public bool $show = false;
    public $products= [];

    public function editProduct(int $productNameId): void
    {
        $this->editedProductNameId = $productNameId;
        $this->product = \App\Models\Topping::find($productNameId);
        $this->name = $this->product->name;
        $this->price = $this->product->price;
        $this->sign = $this->product->sign;
        $this->show = $this->product->show;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'sign' => 'required'
        ]);

        if (is_null($this->product)) {
            $position = \App\Models\Topping::max('position') + 1;
            \App\Models\Topping::create(array_merge($this->only('name', 'price', 'show', 'sign'), ['position' => $position]));
        } else {
            $this->product->update($this->only('name', 'price', 'show', 'sign'));
        }

        $this->editedProductNameId = 0;
        $this->mount();
    }

    public function deleteProduct(int $productNameId): void
    {
        \App\Models\Topping::destroy($productNameId);
        $this->mount();
    }

    #[on('productAdded')]
    public function mount(): void
    {
        $this->products = \App\Models\Topping::all();
    }
    public function cancel(): void
    {
        $this->editedProductNameId = 0;
    }

    public function render()
    {
        return view('livewire.menu.tables.toppings-table');
    }
}
