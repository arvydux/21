<?php

namespace App\Livewire\Menu\Tables;

use App\Livewire\Concerns\AppliesValueToAllProducts;
use App\Livewire\Concerns\SetsKitchenForAllProducts;
use Livewire\Attributes\On;
use Livewire\Component;

class PotatoDishesTable extends Component
{
    use AppliesValueToAllProducts, SetsKitchenForAllProducts;

    public int $editedProductNameId = 0;

    public \App\Models\PotatoDish $product;

    public ?int $number = null;

    public string $name;

    public $price;

    public $half_price;

    public $package = null;

    public string $sign;

    public bool $show = false;

    public ?int $subcategory_id = null;

    public $products = [];

    public function editProduct(int $productNameId): void
    {
        $this->editedProductNameId = $productNameId;
        $this->product = \App\Models\PotatoDish::find($productNameId);
        $this->number = $this->product->number;
        $this->name = $this->product->name;
        $this->price = $this->product->price;
        $this->half_price = $this->product->half_price;
        $this->sign = $this->product->sign;
        $this->show = $this->product->show;
        $this->for_kitchen = (bool) $this->product->for_kitchen;
        $this->package = $this->product->package;
        $this->subcategory_id = $this->product->subcategory_id;
    }

    public function save()
    {
        $this->normalizeMoney('price', 'package', 'half_price');

        $this->validate([
            'number' => 'nullable|integer|min:1',
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'package' => 'nullable|numeric|min:0',
            'half_price' => 'required|numeric|min:0',
            'subcategory_id' => 'required|exists:subcategories,id',
        ], $this->moneyMessages('price', 'package', 'half_price'));

        if (is_null($this->product)) {
            $position = \App\Models\PotatoDish::max('position') + 1;
            \App\Models\PotatoDish::create(array_merge($this->only('number', 'name', 'price', 'half_price', 'show', 'for_kitchen', 'sign', 'package', 'subcategory_id'), ['position' => $position]));
        } else {
            $this->product->update($this->only('number', 'name', 'price', 'half_price', 'show', 'for_kitchen', 'sign', 'package', 'subcategory_id'));
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
        $this->products = \App\Models\PotatoDish::orderByRaw('number is null')->orderBy('number')->orderBy('position')->get();
    }

    public function cancel(): void
    {
        $this->editedProductNameId = 0;
    }

    #[On('subcategoryChanged')]
    public function refreshSubcategories(): void {}

    /**
     * @return array<string, string>
     */
    protected function bulkFields(): array
    {
        return [
            'package' => 'Pakuotės kaina',
            'price' => 'Pilnos porcijos kaina',
            'half_price' => 'Pusės porcijos kaina',
        ];
    }

    protected function bulkModel(): string
    {
        return \App\Models\PotatoDish::class;
    }

    public function render()
    {
        return view('livewire.menu.tables.potato-dishes-table', [
            'subcategories' => \App\Models\Subcategory::whereHas('category', fn ($q) => $q->where('component_name', 'bulviniai'))->orderBy('position')->get(),
        ]);
    }
}
