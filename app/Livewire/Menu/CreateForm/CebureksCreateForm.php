<?php

namespace App\Livewire\Menu\CreateForm;

use App\Models\Subcategory;
use Livewire\Attributes\On;
use Livewire\Component;

class CebureksCreateForm extends Component
{
    public $name;

    public $price;

    public $sign;

    public $subcategory_id;

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);

        // Save to database
        \App\Models\Ceburek::create([
            'name' => $this->name,
            'price' => $this->price,
            'sign' => 'A',
            'subcategory_id' => $this->subcategory_id,
        ]);

        // Reset form
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
        return view('livewire.menu.create-form.cebureks-create-form', [
            'subcategories' => Subcategory::whereHas('category', fn ($q) => $q->where('component_name', 'ceburekai'))->orderBy('position')->get(),
        ]);
    }
}
