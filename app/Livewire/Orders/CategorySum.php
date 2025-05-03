<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Component;

class CategorySum extends Component
{
    public float $packageSum = 0;
    public float $drinksSum = 0;
    public float $bakerySum = 0;

    public function summarizePackages()
    {
        $this->packageSum = 0;
        $ordersWithPackages =  Order::where('package', true)->get();
        foreach ($ordersWithPackages as $order) {
            $this->packageSum += $order->amount * 0.3;
        }
    }

    public function summarizeDrinks()
    {
        $this->drinksSum = 0;
        $ordersWithPackages =  Order::where('category', 3)->get();
        foreach ($ordersWithPackages as $order) {
            $this->drinksSum += $order->amount * $order->order_price;
        }
    }

    public function summarizeBakery()
    {
        $this->bakerySum = 0;
        $ordersWithPackages =  Order::where('category', '!=', 3)->get();
        foreach ($ordersWithPackages as $order) {
            $this->bakerySum += $order->amount * $order->order_price;
        }
    }

    #[On('change-order')]
    public function mount()
    {
        $this->summarizePackages();
        $this->summarizeDrinks();
        $this->summarizeBakery();
    }

    public function render()
    {
        return view('livewire.orders.category-sum');
    }
}
