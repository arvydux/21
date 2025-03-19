<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Component;

class TotalSumManager extends Component
{
    public float $totalSum = 0;

    #[On('change-order')]
    public function summarize()
    {
        $this->totalSum = 0;
        $orders = Order::all();
        foreach ($orders as $order) {
            for ($i = 0; $i < $order->amount; $i++) {
                foreach (json_decode($order->name) as $key => $name) {
                    foreach ($name as $product => $price) {
                        $this->totalSum += $price;
                    }
                }
                if (isset($order->toppings)) {
                    foreach (json_decode($order->toppings) as $topping) {
                        foreach ($topping as $name => $price) {
                            $this->totalSum += $price;
                        }
                    }
                }
            }
        }
    }

    public function mount()
    {
        $this->summarize();
    }

    public function render()
    {
        return view('livewire.total-sum-manager');
    }
}
