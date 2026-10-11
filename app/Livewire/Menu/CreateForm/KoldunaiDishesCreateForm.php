<?php

namespace App\Livewire\Menu\CreateForm;

use App\Models\Subcategory;
use Livewire\Attributes\On;
use Livewire\Component;

class KoldunaiDishesCreateForm extends Component
{
    public $number;

    public $name;

    public $price;

    public $sign;

    public $subcategory_id;

    public function save()
    {
        $this->validate([
            'number' => 'nullable|integer|min:1',
            'name' => 'required',
            'price' => 'required|numeric',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);

        // Save to database
        \App\Models\KoldunaiDish::create([
            'number' => $this->number ?: null,
            'name' => $this->name,
            'price' => $this->price,
            'sign' => 'A',
            'position' => \App\Models\KoldunaiDish::max('position') + 1,
            'subcategory_id' => $this->subcategory_id,
        ]);

        // Reset form
        $this->number = '';
        $this->name = '';
        $this->price = '';
        $this->sign = '';
        $this->subcategory_id = null;

        $this->dispatch('productAdded');
    }

    #[On('subcategoryChanged')]
    public function refreshSubcategories(): void {}

    public function render()
    {
        return view('livewire.menu.create-form.koldunai-dishes-create-form', [
            'subcategories' => Subcategory::whereHas('category', fn ($q) => $q->where('component_name', 'koldunai'))->orderBy('position')->get(),
        ]);
    }
}
