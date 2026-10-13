<?php

namespace App\Livewire\Menu\Tables;

use App\Livewire\Concerns\AppliesValueToAllProducts;
use Livewire\Attributes\On;
use Livewire\Component;

class ToppingsTable extends Component
{
    use AppliesValueToAllProducts;

    public int $editedProductNameId = 0;

    public \App\Models\Topping $product;

    public string $name;

    public $price;

    public string $sign;

    public $package = null;

    public bool $show = false;

    public $products = [];

    public function editProduct(int $productNameId): void
    {
        $this->editedProductNameId = $productNameId;
        $this->product = \App\Models\Topping::find($productNameId);
        $this->name = $this->product->name;
        $this->price = $this->product->price;
        $this->sign = $this->product->sign;
        $this->show = $this->product->show;
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
            $position = \App\Models\Topping::max('position') + 1;
            \App\Models\Topping::create(array_merge($this->only('name', 'price', 'show', 'sign', 'package'), ['position' => $position]));
        } else {
            $this->product->update($this->only('name', 'price', 'show', 'sign', 'package'));
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
        return \App\Models\Topping::class;
    }

    public function render()
    {
        return view('livewire.menu.tables.toppings-table');
    }
}
