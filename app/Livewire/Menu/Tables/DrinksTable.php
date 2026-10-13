<?php

namespace App\Livewire\Menu\Tables;

use App\Livewire\Concerns\AppliesValueToAllProducts;
use Livewire\Attributes\On;
use Livewire\Component;

class DrinksTable extends Component
{
    use AppliesValueToAllProducts;

    public int $editedProductNameId = 0;

    public \App\Models\Drink $simpleProduct;

    public ?int $number = null;

    public string $name;

    public $price;

    public $package = null;

    public bool $show = false;

    public ?int $subcategory_id = null;

    public $simpleProducts = [];

    public function editProduct(int $productNameId): void
    {
        $this->editedProductNameId = $productNameId;
        $this->simpleProduct = \App\Models\Drink::find($productNameId);
        $this->number = $this->simpleProduct->number;
        $this->name = $this->simpleProduct->name;
        $this->price = $this->simpleProduct->price;
        $this->show = $this->simpleProduct->show;
        $this->package = $this->simpleProduct->package;
        $this->subcategory_id = $this->simpleProduct->subcategory_id;
    }

    public function save()
    {
        $this->normalizeMoney('price', 'package');

        $this->validate([
            'number' => 'nullable|integer|min:1',
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'package' => 'nullable|numeric|min:0',
            'subcategory_id' => 'required|exists:subcategories,id',
        ], $this->moneyMessages('price', 'package'));

        if (is_null($this->simpleProduct)) {
            $position = \App\Models\Drink::max('position') + 1;
            \App\Models\Drink::create(array_merge($this->only('number', 'name', 'price', 'show', 'package', 'subcategory_id'), ['position' => $position]));
        } else {
            $this->simpleProduct->update($this->only('number', 'name', 'price', 'show', 'package', 'subcategory_id'));
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
        $this->simpleProducts = \App\Models\Drink::orderByRaw('number is null')->orderBy('number')->orderBy('position')->get();
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
            'price' => 'Kaina',
        ];
    }

    protected function bulkModel(): string
    {
        return \App\Models\Drink::class;
    }

    public function render()
    {
        return view('livewire.menu.tables.drinks-table', [
            'subcategories' => \App\Models\Subcategory::whereHas('category', fn ($q) => $q->where('component_name', 'drinks'))->orderBy('position')->get(),
        ]);
    }
}
