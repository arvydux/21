<?php

namespace App\Livewire\Menu\CreateForm;

use App\Models\Subcategory;
use Livewire\Attributes\On;
use Livewire\Component;

class OtherProductsCreateForm extends Component
{
    public $name;

    public $price;

    public $subcategory_id;

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);

        // Save to database
        \App\Models\OtherProduct::create([
            'name' => $this->name,
            'price' => $this->price,
            'subcategory_id' => $this->subcategory_id,
        ]);

        // Reset form
        $this->name = '';
        $this->price = '';
        $this->subcategory_id = null;

        $this->dispatch('productAdded');
    }

    #[On('subcategoryChanged')]
    public function refreshSubcategories(): void {}

    public function render()
    {
        return view('livewire.menu.create-form.other-products-create-form', [
            'subcategories' => Subcategory::whereHas('category', fn ($q) => $q->where('component_name', 'other'))->orderBy('position')->get(),
        ]);
    }
}
