<?php

namespace App\Livewire\Menu\CreateForm;

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
        ]);

        // Save to database
        \App\Models\Ceburek::create([
            'name' => $this->name,
            'price' => $this->price,
            'sign' => 'A',
        ]);

        // Reset form
        $this->name = '';
        $this->price = '';
        $this->sign = '';

        $this->dispatch('productAdded');
    }

    public function render()
    {
        return view('livewire.menu.create-form.cebureks-create-form');
    }
}
