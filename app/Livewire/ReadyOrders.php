<?php

namespace App\Livewire;

use App\Events\OrdersUpdated;
use App\Models\OrderNumbers;
use Livewire\Attributes\On;
use Livewire\Component;

class ReadyOrders extends Component
{
    #[On('echo:orders,OrdersUpdated')]
    public function handleOrdersUpdated(): void {}

    public function makeOrderTaken($number): void
    {
        OrderNumbers::where('number', $number)->update(['is_taken' => true]);
        OrdersUpdated::dispatch();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.ready-orders');
    }
}
