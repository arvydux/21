<?php

namespace App\Livewire\Menu\CreateForm;

use Livewire\Component;

class DrinksCreateForm extends Component
{
    public $name;
    public $price;

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
        ]);

        // Save to database
        \App\Models\Drink::create([
            'name' => $this->name,
            'price' => $this->price,
        ]);

        // Reset form
        $this->name = '';
        $this->price = '';

        $this->dispatch('productAdded');
    }

    public function render()
    {
        return view('livewire.menu.create-form.drinks-create-form');
    }
}
