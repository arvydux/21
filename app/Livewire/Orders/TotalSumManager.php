<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
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

        $packages = 0;
        $ordersWithPackages = Order::whereNotNull('package')->get();
        foreach ($ordersWithPackages as $order) {
            $packages += $order->amount * $order->package;
        }

        $this->totalSum += $packages;
        Cache::put('totalSum', $this->totalSum, 60 * 60 * 24); // Cache for 24 hours
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
