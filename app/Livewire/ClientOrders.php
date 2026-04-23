<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class ClientOrders extends Component
{
    #[On('echo:orders,OrdersUpdated')]
    public function handleOrdersUpdated(): void {}

    public function render(): \Illuminate\View\View
    {
        return view('livewire.client-orders');
    }
}
