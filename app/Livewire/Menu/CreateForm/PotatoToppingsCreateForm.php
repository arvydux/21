<?php

namespace App\Livewire\Menu\CreateForm;

use App\Livewire\Concerns\NormalizesMoneyInput;
use Livewire\Component;

class PotatoToppingsCreateForm extends Component
{
    use NormalizesMoneyInput;

    public $name;

    public $price;

    public $sign;

    public function save()
    {
        $this->normalizeMoney('price');

        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:0',
        ], $this->moneyMessages('price'));

        // Save to database
        \App\Models\PotatoTopping::create([
            'name' => $this->name,
            'price' => $this->price,
            'sign' => 'A',
            'position' => \App\Models\PotatoTopping::max('position') + 1,
        ]);

        // Reset form
        $this->name = '';
        $this->price = '';
        $this->sign = '';

        $this->dispatch('productAdded');
    }

    public function render()
    {
        return view('livewire.menu.create-form.potato-toppings-create-form');
    }
}
