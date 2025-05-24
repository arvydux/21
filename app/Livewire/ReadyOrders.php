<?php

namespace App\Livewire;

use App\Models\OrderNumbers;
use Livewire\Component;

class ReadyOrders extends Component
{


    public function makeOrderTaken($number): void
    {
        OrderNumbers::where('number', $number)->update(['is_taken' => true]);
    }

}
