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
        $numbers = Cache::get('pendingSounds', []);
        if ($this->soundEnabled && count($numbers) > 0) {
            Cache::put('pendingSounds', [], 60 * 60 * 24);
            $this->dispatch('play-sound', numbers: $numbers);
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
