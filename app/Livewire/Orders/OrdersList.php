<?php

namespace App\Livewire\Orders;

use AllowDynamicProperties;
use App\Models\Order;
use App\Models\FreeNumbers;
use App\Models\OrderNumbers;
use Livewire\Attributes\On;
use Livewire\Component;

class OrdersList extends Component
{
    public $orders = [];
    public $toppings = [];
    public int $freeNumber;
    public bool $byPhone;


    #[on('change-order')]
    public function fetchOrders()
    {
        $this->orders = Order::orderBy('category')->get();
    }

    public function makeOrder()
    {
        $this->makeOrderNumber();

        $this->dispatch('order-made');
    }

    public function addOneOrder($id)
    {
        $order = Order::find($id);
        $order->amount += 1;
        $order->save();
        $this->dispatch('change-order');
    }

    public function removeOneOrder($id)
    {
        $order = Order::find($id);
        $order->amount -= 1;
        $order->save();
        if ($order->amount === 0){
            $this->removeOrder($id);
        }
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

    private function makeOrderNumber(): int
    {
        $this->freeNumber = FreeNumbers::first()->number;
        OrderNumbers::create([
            'number' => $this->freeNumber,
            'by_phone' => $this->byPhone,
            'created_at' => now(),
        ]);
        FreeNumbers::first()->update(['number' => $this->freeNumber + 1]);
        $this->byPhone = false;

        return $this->freeNumber;
    }

    public function mount()
    {
        $this->byPhone = false;
        $this->orders = Order::all();
    }

    public function render()
    {
        return view('livewire.orders.orders-list');
    }
}
