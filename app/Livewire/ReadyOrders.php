<?php

namespace App\Livewire;

use App\Models\OrderNumbers;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ReadyOrders extends Component
{
    public bool $soundEnabled = false;

    public function enableSound(): void
    {
        $this->soundEnabled = true;
    }

    public function checkAndPlaySound(): void
    {
        $count = (int) Cache::get('playSound', 0);
        if ($this->soundEnabled && $count > 0) {
            Cache::put('playSound', 0, 60 * 60 * 24);
            $this->dispatch('play-sound', count: $count);
        }
    }

    public function makeOrderTaken($number): void
    {
        OrderNumbers::where('number', $number)->update(['is_taken' => true]);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.ready-orders');
    }
}
