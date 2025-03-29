<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class DrinksTable extends Component
{
    public int $editedProductNameId = 0;
    public \App\Models\Drink $simpleProduct;
    public string $name;
    public string $price;
    public bool $show = false;
    public $simpleProducts= [];

    public function editProduct(int $productNameId): void
    {
        $this->editedProductNameId = $productNameId;
        $this->simpleProduct = \App\Models\Drink::find($productNameId);
        $this->name = $this->simpleProduct->name;
        $this->price = $this->simpleProduct->price;
        $this->show = $this->simpleProduct->show;
    }

    public function save()
    {
        // $this->validate();

        if (is_null($this->simpleProduct)) {
            $position = \App\Models\Drink::max('position') + 1;
            \App\Models\Drink::create(array_merge($this->only('name', 'price', 'show'), ['position' => $position]));
        } else {
            $this->simpleProduct->update($this->only('name', 'price', 'show'));
        }

        $this->editedProductNameId = 0;
        $this->mount();
    }

    public function deleteProduct(int $productNameId): void
    {
        \App\Models\Drink::destroy($productNameId);
        $this->mount();
    }

    #[on('productAdded')]
    public function mount(): void
    {
        $this->simpleProducts = \App\Models\Drink::all();
    }
    public function cancel(): void
    {
        $this->editedProductNameId = 0;
    }

    public function render()
    {
        return view('livewire.drinks-table');
    }
}
