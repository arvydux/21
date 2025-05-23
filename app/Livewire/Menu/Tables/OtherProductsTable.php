<?php

namespace App\Livewire\Menu\Tables;

use Livewire\Attributes\On;
use Livewire\Component;

class OtherProductsTable extends Component
{
    public int $editedProductNameId = 0;
    public \App\Models\OtherProduct $simpleProduct;
    public string $name;
    public float $price;
    public ?float $package = null;
    public bool $show = false;
    public $simpleProducts= [];

    public function editProduct(int $productNameId): void
    {
        $this->editedProductNameId = $productNameId;
        $this->simpleProduct = \App\Models\OtherProduct::find($productNameId);
        $this->name = $this->simpleProduct->name;
        $this->price = $this->simpleProduct->price;
        $this->show = $this->simpleProduct->show;
        $this->package = $this->simpleProduct->package;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
        ]);

        if (is_null($this->simpleProduct)) {
            $position = \App\Models\OtherProduct::max('position') + 1;
            \App\Models\OtherProduct::create(array_merge($this->only('name', 'price', 'show', 'package'), ['position' => $position]));
        } else {
          //  dd($this->packagePrice);
            $this->simpleProduct->update($this->only('name', 'price', 'show', 'package'));
        }

        $this->editedProductNameId = 0;
        $this->mount();
    }

    public function deleteProduct(int $productNameId): void
    {
        \App\Models\OtherProduct::destroy($productNameId);
        $this->mount();
    }

    #[on('productAdded')]
    public function mount(): void
    {
        $this->simpleProducts = \App\Models\OtherProduct::all();
    }
    public function cancel(): void
    {
        $this->editedProductNameId = 0;
    }

    public function render()
    {
        return view('livewire.menu.tables.other-products-table');
    }
}
