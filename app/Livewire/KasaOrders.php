<?php

namespace App\Livewire;

use App\Models\OrderNumbers;
use Livewire\Component;

class KasaOrders extends Component
{
    public  bool $showTakenOrders = false;
    public function toogleTakenOrders()
    {
        $this->showTakenOrders = !$this->showTakenOrders;
    }

    public function makeOrderTaken($number): void
    {
        OrderNumbers::where('number', $number)->update(['is_taken' => true]);
    }
    public function makeOrderNotTaken($number): void
    {
        $this->showTakenOrders = false;
        OrderNumbers::where('number', $number)->update(['is_taken' => false]);
    }
}
