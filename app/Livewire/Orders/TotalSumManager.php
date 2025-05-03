<?php

namespace App\Livewire\Orders;

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

        $packages = 0;$ordersWithPackages =  Order::where('package', true)->get();
        foreach ($ordersWithPackages as $order) {
            $packages += $order->amount * 0.3;
        }

        $this->totalSum += $packages;
    }

    public function mount()
    {
        $this->summarize();
    }

    public function render()
    {
        return view('livewire.orders.total-sum-manager');
    }
}
