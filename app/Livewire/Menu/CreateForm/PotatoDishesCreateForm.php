<?php

namespace App\Livewire\Menu\CreateForm;

use App\Models\Subcategory;
use Livewire\Attributes\On;
use Livewire\Component;

class PotatoDishesCreateForm extends Component
{
    public $name;

    public $price;

    public $half_price;

    public $sign;

    public $subcategory_id;

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'half_price' => 'required|numeric',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);

        // Save to database
        \App\Models\PotatoDish::create([
            'name' => $this->name,
            'price' => $this->price,
            'half_price' => $this->half_price,
            'sign' => 'A',
            'position' => \App\Models\PotatoDish::max('position') + 1,
            'subcategory_id' => $this->subcategory_id,
        ]);

        // Reset form
        $this->name = '';
        $this->price = '';
        $this->half_price = '';
        $this->sign = '';
        $this->subcategory_id = null;

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
