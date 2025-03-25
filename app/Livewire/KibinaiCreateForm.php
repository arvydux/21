<?php

namespace App\Livewire;

use Livewire\Component;

class KibinaiCreateForm extends Component
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
        \App\Models\SimpleProduct::create([
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
        return view('livewire.kibinai-create-form');
    }
}
