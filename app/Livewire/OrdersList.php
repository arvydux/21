<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Component;

class OrdersList extends Component
{
    public $orders = [];
    public $toppings = [];


    #[on('change-order')]
    public function fetchOrders()
    {
        $this->orders = Order::all();
    }

    public function makeOrder()
    {
        $this->dispatch('order-made');
    }

    public function addOneOrder($id)
    {
        $order = Order::find($id);
        $order->amount += 1;
        $order->save();
        $this->dispatch('change-order');
    }
    public function removeOrder($id)
    {
        Order::find($id)->delete();
        $this->dispatch('change-order');
    }

    public function resetOrders()
    {
        Order::truncate();
        $this->dispatch('change-order');
        $this->dispatch('reset-orders');
    }

    public function mount()
    {
        $this->orders = Order::all();
    }

    public function render()
    {
        return view('livewire.orders-list');
    }
}
