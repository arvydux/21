<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderManager extends Component
{
    public $orderName;
    public $toppings = [];
    public $allProducts;
    public $allToppings;

    public float $orderPrice = 0;

    public $productName;

    #[on('chooseProductName')]
    public function getProductName($name)
    {
        $this->productName = $name;
    }

    public function mount()
    {
        $this->getToppingList();
        $this->getProductList();
    }

    public function getProductList()
    {
        $this->allProducts = \App\Models\Product::all();
    }

    public function getToppingList()
    {
        $this->allToppings = \App\Models\Topping::all();
    }

    #[on('simple-product-added')]
    public function addOrder()
    {
        $productPrices = $this->allProducts->pluck('price', 'name')->toArray();
        $toppingPrices = $this->allToppings->pluck('price', 'name')->toArray();
        $this->orderPrice = 0;
        $toppingsWithPrices = [];
        $this->orderPrice += $productPrices[$this->productName];
        $productWithPrice[] = [$this->productName => $productPrices[$this->productName]];
        sort($this->toppings);
        foreach ($this->toppings as $topping) {
            $this->orderPrice += $toppingPrices[$topping];
            $toppingsWithPrices[] = [$topping => $toppingPrices[$topping]];
        }

        if (empty($toppingsWithPrices)) {
            $sameOrderWithNoToppings = Order::whereJsonContains('name', $productWithPrice)
                ->whereJsonLength('toppings', 0)->first() ?? null;
            if ($sameOrderWithNoToppings) {
                Order::where('id', $sameOrderWithNoToppings->id)->update([
                    'amount' => $sameOrderWithNoToppings->amount + 1,
                ]);
            } else {
                Order::create([
                    'name' => json_encode($productWithPrice),
                    'toppings' => json_encode($toppingsWithPrices),
                ]);
            }
        }

        if (!empty($toppingsWithPrices)) {
            $sameOrder = Order::whereJsonContains('name', $productWithPrice)
                ->whereJsonContains('toppings', $toppingsWithPrices)->first() ?? null;
            if ($sameOrder) {
                Order::where('id', $sameOrder->id)->update([
                    'amount' => $sameOrder->amount + 1,
                ]);
            } else {
                Order::create([
                    'name' => json_encode($productWithPrice),
                    'toppings' => json_encode($toppingsWithPrices),
                ]);
            }
        }

        $this->dispatch('change-order', orderName: $this->orderName);
        $this->toppings = [];
    }

    public function render()
    {
        return view('livewire.order-manager');
    }
}
