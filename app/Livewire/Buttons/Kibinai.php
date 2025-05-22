<?php

namespace App\Livewire\Buttons;

use App\Models\Order;
use Livewire\Component;

class Kibinai extends Component
{
    public $productName;
    public $selectedProductName = '';
    public function addProduct($productName, $productPrice)
    {
        $this->selectedProductName = $productName;
        $productWithPrice[] = [$productName => $productPrice];
        $sameOrder = Order::whereJsonContains('name', $productWithPrice)->first() ?? null;
        if ($sameOrder) {
            Order::where('id', $sameOrder->id)->update([
                'amount' => $sameOrder->amount + 1,
            ]);
        } else {
            Order::create([
                'name' => json_encode([[$productName => $productPrice]]),
                'order_price' => $productPrice,
                'package' => \App\Models\Kibinai::where('name', $productName)->first()->package,
                'category' => 2,
            ]);
        }

        $this->dispatch('change-order', orderName:$productName);
    }

    public function render()
    {
        return view('livewire.buttons.kibinai');
    }
}
