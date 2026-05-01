<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class ClientOrders extends Component
{
    #[On('echo:orders,OrdersUpdated')]
    public function handleOrdersUpdated(): void {}

    #[On('echo:orders,OrderReady')]
    public function handleOrderReady(array $event): void
    {
        $this->dispatch('play-order-ready', number: (int) $event['number']);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.client-orders');
    }
}
