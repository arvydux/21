<?php

namespace App\Livewire;

use Livewire\Component;

class Product extends Component
{
    public $productName;

    public function getProductName()
    {
        $this->dispatch('chooseProductName', name: $this->productName);
    }

    public function render()
    {
        return view('livewire.product');
    }
}
