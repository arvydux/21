<?php

namespace App\Livewire\Menu\Tables;

use App\Livewire\Concerns\AppliesValueToAllProducts;
use Livewire\Attributes\On;
use Livewire\Component;

class PotatoToppingsTable extends Component
{
    use AppliesValueToAllProducts;

    public int $editedProductNameId = 0;

    public \App\Models\PotatoTopping $product;

    public string $name;

    public $price;

    public string $sign;

    public $package = null;

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
        $this->normalizeMoney('price', 'package');

        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'package' => 'nullable|numeric|min:0',
            'sign' => 'required',
        ], $this->moneyMessages('price', 'package'));

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

    /**
     * @return array<string, string>
     */
    protected function bulkFields(): array
    {
        return [
            'package' => 'Pakuotės kaina',
            'price' => 'Kaina',
        ];
    }

    protected function bulkModel(): string
    {
        return \App\Models\PotatoTopping::class;
    }

    public function render()
    {
        return view('livewire.menu.tables.potato-toppings-table');
    }
}
