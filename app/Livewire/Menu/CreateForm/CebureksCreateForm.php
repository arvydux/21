<?php

namespace App\Livewire\Menu\CreateForm;

use App\Livewire\Concerns\NormalizesMoneyInput;
use App\Models\Subcategory;
use Livewire\Attributes\On;
use Livewire\Component;

class CebureksCreateForm extends Component
{
    use NormalizesMoneyInput;

    public $number;

    public $name;

    public $price;

    public $sign;

    public $subcategory_id;

    public bool $for_kitchen = true;

    public function save()
    {
        $this->normalizeMoney('price');

        $this->validate([
            'number' => 'nullable|integer|min:1',
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'subcategory_id' => 'required|exists:subcategories,id',
        ], $this->moneyMessages('price'));

        // Save to database
        \App\Models\Ceburek::create([
            'number' => $this->number ?: null,
            'name' => $this->name,
            'price' => $this->price,
            'sign' => 'A',
            'subcategory_id' => $this->subcategory_id,
            'for_kitchen' => $this->for_kitchen,
        ]);

        // Reset form
        $this->number = '';
        $this->name = '';
        $this->price = '';
        $this->sign = '';
        $this->subcategory_id = null;
        $this->reset('for_kitchen');

        $this->dispatch('productAdded');
    }

    #[On('subcategoryChanged')]
    public function refreshSubcategories(): void {}

    public function render()
    {
        return view('livewire.menu.create-form.cebureks-create-form', [
            'subcategories' => Subcategory::whereHas('category', fn ($q) => $q->where('component_name', 'ceburekai'))->orderBy('position')->get(),
        ]);
    }
}
