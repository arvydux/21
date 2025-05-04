<?php

namespace App\Livewire\Orders;

use App\Models\FreeNumbers;
use App\Models\Order;
use App\Models\OrderNumbers;
use Exception;
use Livewire\Attributes\On;
use Livewire\Component;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class OrdersList extends Component
{
    public $orders = [];
    public $toppings = [];
    public int $freeNumber;
    public bool $byPhone;
    private int $limit = 100;


    #[on('change-order')]
    public function fetchOrders()
    {
        $this->orders = Order::orderBy('category')->get();
    }

    public function makeOrder()
    {
        $this->makeOrderNumber();

        $this->dispatch('order-made');
    }

    public function addOneOrder($id)
    {
        $order = Order::find($id);
        $order->amount += 1;
        $order->save();
        $this->dispatch('change-order');
    }

    public function removeOneOrder($id)
    {
        $order = Order::find($id);
        $order->amount -= 1;
        $order->save();
        if ($order->amount === 0){
            $this->removeOrder($id);
        }
        $this->dispatch('change-order');
    }

    public function removeOrder($id)
    {
        Order::find($id)->delete();
        $this->dispatch('change-order');
    }

    public function resetOrders()
    {
        Order::truncate();
        $this->dispatch('change-order');
        $this->dispatch('reset-orders');
    }

    private function makeOrderNumber(): int
    {
        // free number maximum value&+
        if (FreeNumbers::first()->number >= $this->limit) {
            FreeNumbers::first()->update(['number' => 1]);
            OrderNumbers::truncate();
        }
        $this->freeNumber = FreeNumbers::first()->number;
        OrderNumbers::create([
            'number'     => $this->freeNumber,
            'by_phone'   => $this->byPhone,
            'created_at' => now(),
        ]);
        FreeNumbers::first()->update(['number' => $this->freeNumber + 1]);
        $this->byPhone = false;
        $this->printOrderForKitchen($this->freeNumber);
        $this->printOrderForClient($this->freeNumber);

        return $this->freeNumber;
    }

    private function printOrderForKitchen(int $freeOrder): void
    {
        try {
            $connector = new WindowsPrintConnector("smb://" . env('COMPUTER_PRINTER_NAME') . "/" . env('PRINTER_FOR_KITCHEN_NAME'));
            $printer = new Printer($connector);
            $printer -> setEmphasis(true);
            $printer->setTextSize(4, 4);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($freeOrder . "\n");
            $printer->setTextSize(2, 1);
            $printer->text("\n" . now() . "\n");
            $printer->text(str_repeat("-", 24) . "\n"); // Adjust 48 to match your printer's width
            foreach (Order::where('category', 1)->orwhere('category', 4)->get() as $order) {
                foreach (json_decode($order->name) as $name) {
                    foreach ($name as $name => $price) {
                        $printer->setJustification();
                        $printer -> setEmphasis(true);
                        $printer->setJustification();
                        $printer->text($name . "\n");
                    }
                }

                if (isset($order->toppings)) {
                    foreach (json_decode($order->toppings) as $topping) {
                        foreach ($topping as  $name => $toppingPrice) {
                            $printer->setJustification(Printer::JUSTIFY_RIGHT);
                            $printer->setTextSize(2, 1);
                            $printer->text($name . "\n");
                        }
                    }
                }
                $printer->setJustification();
                $printer->setTextSize(2, 1);
                $takeaway = $order->takeaway ? 'Vietoje' : 'Išsinešimui';
                $printer->text($takeaway . "\n");
                $printer->text($order->amount . ' vnt.' . "\n");
                $printer->text(str_repeat("-", 24) . "\n"); // Adjust 48 to match your printer's width

            }

            $printer->cut();
            /* Close printer */
            $printer->close();
        }
        catch
        (Exception $e) {
            echo "Couldn't print to this printer: " . $e->getMessage() . "\n";
        }
    }

    private function printOrderForClient(int $freeOrder): void
    {
        try {
            $connector = new WindowsPrintConnector("smb://" . env('COMPUTER_PRINTER_NAME') . "/" . env('PRINTER_FOR_CLIENT_NAME'));
            $printer = new Printer($connector);
            $printer -> setEmphasis(true);
            $printer->setTextSize(4, 4);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($freeOrder . "\n");
            $printer->setTextSize(2, 1);
            $printer->text("\n" . now() . "\n");
            $printer->text(str_repeat("-", 24) . "\n"); // Adjust 48 to match your printer's width
            $printer->cut();
            /* Close printer */
            $printer->close();
        }
        catch
        (Exception $e) {
            echo "Couldn't print to this printer: " . $e->getMessage() . "\n";
        }
    }

    public function mount()
    {
        $this->byPhone = false;
        $this->orders = Order::all();
    }

    public function render()
    {
        return view('livewire.orders.orders-list');
    }
}
