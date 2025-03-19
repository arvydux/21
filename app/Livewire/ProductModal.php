<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class ProductModal extends Component
{
    public $productName;

    #[on('chooseProductName')]
    public function getProductName($name)
    {
        $this->productName = $name;
    }


    public function render()
    {
        return view('livewire.product-modal');
    }
}
