<?php

namespace App\Livewire\Menu\Tables;

use Livewire\Attributes\On;
use Livewire\Component;

class KibinaiTable extends Component
{
    public int $editedProductNameId = 0;
    public \App\Models\Kibinai $simpleProduct;
    public string $name;
    public float $price;
    public ?float $package = null;
    public bool $show = false;
    public $simpleProducts= [];

    public function editProduct(int $productNameId): void
    {
        $this->editedProductNameId = $productNameId;
        $this->simpleProduct = \App\Models\Kibinai::find($productNameId);
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
            $position = \App\Models\Kibinai::max('position') + 1;
            \App\Models\Kibinai::create(array_merge($this->only('name', 'price', 'show', 'package'), ['position' => $position]));
        } else {
            $this->simpleProduct->update($this->only('name', 'price', 'show', 'package'));
        }

        $this->editedProductNameId = 0;
        $this->mount();
    }

    public function deleteProduct(int $productNameId): void
    {
        \App\Models\Kibinai::destroy($productNameId);
        $this->mount();
    }

    #[on('productAdded')]
    public function mount(): void
    {
        $this->simpleProducts = \App\Models\Kibinai::all();
    }
    public function cancel(): void
    {
        $this->editedProductNameId = 0;
    }

    public function render()
    {
        return view('livewire.menu.tables.kibinai-table');
    }
}
