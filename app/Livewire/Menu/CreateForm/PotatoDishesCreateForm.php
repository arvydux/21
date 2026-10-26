<?php

namespace App\Livewire\Menu\CreateForm;

use App\Livewire\Concerns\NormalizesMoneyInput;
use App\Models\Subcategory;
use Livewire\Attributes\On;
use Livewire\Component;

class PotatoDishesCreateForm extends Component
{
    use NormalizesMoneyInput;

    public $number;

    public $name;

    public $price;

    public $half_price;

    public $sign;

    public $subcategory_id;

    public bool $for_kitchen = true;

    public function save()
    {
        $this->normalizeMoney('price', 'half_price');

        $this->validate([
            'number' => 'nullable|integer|min:1',
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'half_price' => 'required|numeric|min:0',
            'subcategory_id' => 'required|exists:subcategories,id',
        ], $this->moneyMessages('price', 'half_price'));

        // Save to database
        \App\Models\PotatoDish::create([
            'number' => $this->number ?: null,
            'name' => $this->name,
            'price' => $this->price,
            'half_price' => $this->half_price,
            'sign' => 'A',
            'position' => \App\Models\PotatoDish::max('position') + 1,
            'subcategory_id' => $this->subcategory_id,
            'for_kitchen' => $this->for_kitchen,
        ]);

        // Reset form
        $this->number = '';
        $this->name = '';
        $this->price = '';
        $this->half_price = '';
        $this->sign = '';
        $this->subcategory_id = null;
        $this->reset('for_kitchen');

        $this->dispatch('productAdded');
    }

    #[On('subcategoryChanged')]
    public function refreshSubcategories(): void {}

    public function render()
    {
        return view('livewire.menu.create-form.potato-dishes-create-form', [
            'subcategories' => Subcategory::whereHas('category', fn ($q) => $q->where('component_name', 'bulviniai'))->orderBy('position')->get(),
        ]);
    }
}
