<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ClientOrders extends Component
{
    public function checkAndShowAnimation(): void
    {
        $numbers = Cache::get('pendingAnimations', []);
        if (count($numbers) > 0) {
            Cache::put('pendingAnimations', [], 60 * 60 * 24);
            $this->dispatch('show-order-ready', numbers: $numbers);
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.client-orders');
    }
}
