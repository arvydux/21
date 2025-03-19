<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Component;

class Kitchen extends Component
{
    public $orders = [];
    public $toppings = [];


    #[on('change-order')]
    public function fetchOrders()
    {
        $this->orders = Order::all();
    }

    public function removeOrder($id)
    {
        Order::find($id)->delete();
        $this->mount();
    }
    public function resetOrders()
    {
        Order::truncate();
        $this->dispatch('change-order');
    }

    public function mount()
    {
        $this->orders = Order::all();
    }

    public function render()
    {
        return view('livewire.kitchen');
    }
}
