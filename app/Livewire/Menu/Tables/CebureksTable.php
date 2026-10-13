<?php

namespace App\Livewire\Menu\Tables;

use App\Livewire\Concerns\AppliesValueToAllProducts;
use Livewire\Attributes\On;
use Livewire\Component;

class CebureksTable extends Component
{
    use AppliesValueToAllProducts;

    public int $editedProductNameId = 0;

    public \App\Models\Ceburek $product;

    public ?int $number = null;

    public string $name;

    public $price;

    public $package = null;

    public string $sign;

    public bool $show = false;

    public ?int $subcategory_id = null;

    public $products = [];

    public function editProduct(int $productNameId): void
    {
        $this->editedProductNameId = $productNameId;
        $this->product = \App\Models\Ceburek::find($productNameId);
        $this->number = $this->product->number;
        $this->name = $this->product->name;
        $this->price = $this->product->price;
        $this->sign = $this->product->sign;
        $this->show = $this->product->show;
        $this->package = $this->product->package;
        $this->subcategory_id = $this->product->subcategory_id;
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

        if (is_null($this->product)) {
            $position = \App\Models\Ceburek::max('position') + 1;
            \App\Models\Ceburek::create(array_merge($this->only('number', 'name', 'price', 'show', 'sign', 'package', 'subcategory_id'), ['position' => $position]));
        } else {
            $this->product->update($this->only('number', 'name', 'price', 'show', 'sign', 'package', 'subcategory_id'));
        }

        $this->editedProductNameId = 0;
        $this->mount();
    }

    public function deleteProduct(int $productNameId): void
    {
        \App\Models\Ceburek::destroy($productNameId);
        $this->mount();
    }

    #[on('productAdded')]
    public function mount(): void
    {
        $this->products = \App\Models\Ceburek::orderByRaw('number is null')->orderBy('number')->orderBy('position')->get();
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
        return \App\Models\Ceburek::class;
    }

    public function render()
    {
        return view('livewire.menu.tables.cebureks-table', [
            'subcategories' => \App\Models\Subcategory::whereHas('category', fn ($q) => $q->where('component_name', 'ceburekai'))->orderBy('position')->get(),
        ]);
    }
}
