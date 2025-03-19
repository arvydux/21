<?php

namespace App\Livewire;

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
            ]);
        }

        $this->dispatch('change-order', orderName:$productName);
    }

    public function render()
    {
        return view('livewire.kibinai');
    }
}
