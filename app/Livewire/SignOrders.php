<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class SignOrders extends Component
{
    public $totalCeburekSignsOrders = [];
    public function makeSignOrder()
    {
        $orders = \App\Models\Order::all();
        $totalCeburekSignsOrders = [];
        foreach ($orders as $order) {
            // checking if product is ceburek according to toppings
            if (isset($order->toppings)) {
                $orderName = json_decode($order->name);
                $productName = array_key_first((array)$orderName[0]);
                $ceburekSign = \App\Models\Ceburek::where('name', $productName)->first()->sign;
                if (!empty($order->toppings)) {
                    $toppings = json_decode($order->toppings);
                    foreach ($toppings as $topping) {
                        $topping = array_key_first((array)$topping);
                        $ceburekSign .= ' + ' . \App\Models\Topping::where('name', $topping)->first()->sign;
                    }
                }
                   $totalCeburekSignsOrders[] = $ceburekSign . ' x ' . $order->amount . ($order->takeaway ? ' -' : ' +')  ;
            }
        }

         $this->totalCeburekSignsOrders = $totalCeburekSignsOrders;
    }

    #[On('order-made')]
    public function mount()
    {
        $this->makeSignOrder();
    }

    #[On('reset-orders')]
    public function resetSignOrder()
    {
        $this->totalCeburekSignsOrders = [];
    }

    public function render()
    {
        return view('livewire.sign-orders');
    }
}
