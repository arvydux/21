<?php

namespace App\Livewire;

use Livewire\Component;

class CebureksCreateForm extends Component
{
    public $name;
    public $price;
    public $sign;

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'sign' => 'required|max:1',
        ]);

        // Save to database
        \App\Models\Ceburek::create([
            'name' => $this->name,
            'price' => $this->price,
            'sign' => $this->sign,
        ]);

        // Reset form
        $this->name = '';
        $this->price = '';
        $this->sign = '';

        $this->dispatch('productAdded');
    }

    public function render()
    {
        return view('livewire.cebureks-create-form');
    }
}
