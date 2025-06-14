<?php

namespace App\Livewire\Buttons;

use App\Models\Ceburek;
use App\Models\Order;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Ceburekai extends Component
{
    public $orderName;
    public $toppings = [];
    public $allProducts;
    public $allToppings;

    public float $orderPrice = 0;

    public $productName;

    public $takeaway = true;
    public Collection $cebureks;

    #[on('chooseProductName')]
    public function getProductName($name): void
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
        $this->allProducts = \App\Models\Ceburek::all();
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
                ->whereJsonLength('toppings', 0)
                ->where('takeaway', $this->takeaway)
                ->first() ?? null;
            if ($sameOrderWithNoToppings) {
                Order::where('id', $sameOrderWithNoToppings->id)->update([
                    'amount' => $sameOrderWithNoToppings->amount + 1,
                ]);
            } else {
                Order::create([
                    'name' => json_encode($productWithPrice),
                    'toppings' => json_encode($toppingsWithPrices),
                    'takeaway' => $this->takeaway,
                    'package' => Ceburek::where('name', $this->productName)->first()->package,
                    'order_price' => $this->orderPrice,
                    'category' => 1,
                ]);
            }
        }

        if (!empty($toppingsWithPrices)) {
            $sameOrder = Order::whereJsonContains('name', $productWithPrice)
                ->whereJsonLength('toppings', count($toppingsWithPrices))
                ->whereJsonContains('toppings', $toppingsWithPrices)
                ->where('takeaway', $this->takeaway)
                ->first() ?? null;
            if ($sameOrder) {
                Order::where('id', $sameOrder->id)->update([
                    'amount' => $sameOrder->amount + 1,
                ]);
            } else {
                Order::create([
                    'name' => json_encode($productWithPrice),
                    'toppings' => json_encode($toppingsWithPrices),
                    'takeaway' => $this->takeaway,
                    'package' => Ceburek::where('name', $this->productName)->first()->package,
                    'order_price' => $this->orderPrice,
                    'category' => 1,
                ]);
            }
        }

        $this->dispatch('change-order', orderName: $this->orderName);
        $this->toppings = [];
    }

    public function updateOrder($list)
    {
        foreach ($list as $item) {
            $product = $this->cebureks->firstWhere('id', $item['value']);

            if ($product['position'] != $item['order']) {
                Ceburek::where('id', $item['value'])->update(['position' => $item['order']]);
            }
        }
    }

    public function render()
    {
        $products = Ceburek::orderBy('position')->paginate(10);
        $this->cebureks = collect($products->items());

        return view('livewire.buttons.ceburekai');
    }
}
