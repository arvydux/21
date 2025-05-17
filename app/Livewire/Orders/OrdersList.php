<?php

namespace App\Livewire\Orders;

use App\Models\FreeNumbers;
use App\Models\Order;
use App\Models\OrderNumbers;
use Exception;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

class OrdersList extends Component
{
    public $orders = [];
    public $toppings = [];
    public int $freeNumber;
    public bool $byPhone;
    public $comments;
    private int $limit = 100;


    #[on('change-order')]
    public function fetchOrders()
    {
        $this->orders = Order::orderBy('created_at', 'desc')->get();
    }

    public function openCommentForOrder($orderId)
    {
        $this->comments = Order::find($orderId)->comments;
    }

    public function makeCommentForOrder($orderId)
    {
        $order = Order::find($orderId);
        $order->comments = $this->comments;
        $order->save();
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
        // free number maximum value
        if (FreeNumbers::first()->number >= $this->limit) {
            FreeNumbers::first()->update(['number' => 1]);
            OrderNumbers::where('is_taken', true)
                ->delete();
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
        $this->resetOrders();

        return $this->freeNumber;
    }

    private function printOrderForKitchen(int $freeOrder): void
    {
        try {
            $profile = CapabilityProfile::load("TM-P80");
            $connector = new NetworkPrintConnector("192.168.1.100", 9100, 2);
            $printer = new Printer($connector, $profile);
            $printer -> setEmphasis(true);
            $printer->setTextSize(4, 4);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($freeOrder . "\n");
            // change font
            $printer-> setFont(1);
            $printer->setTextSize(2, 1);
            $printer->text("\n" . now('Europe/Vilnius') . "\n");
            $printer->text(str_repeat("-", 24) . "\n"); // Adjust 48 to match your printer's width
            $printer->feed();
            $printer->setJustification();
            // eksperimental part
            // space between lines
            $printer->setLineSpacing(1);
            // set font 0-default, 1-other
            $printer-> setFont(0);

            foreach (Order::where('category', 1)->orwhere('category', 4)->orderBy('category')->get() as $order) {
                foreach (json_decode($order->name) as $name) {
                    foreach ($name as $name => $price) {
                        $nameInNotLt = $this->convertTextFromLtToNotLt($name);
                        // change font
                        $printer-> setFont(0);
                        $printer->setTextSize(2, 1);
                        $printer->text($nameInNotLt . "\n");
                    }
                }

                if (isset($order->toppings)) {
                    $printer->feed();
                    foreach (json_decode($order->toppings) as $topping) {
                        foreach ($topping as  $name => $toppingPrice) {
                            $printer->setJustification(Printer::JUSTIFY_RIGHT);
                            // change font
                            $printer-> setFont(1);
                            $printer->setTextSize(2, 1);
                            $nameInNotLt = $this->convertTextFromLtToNotLt($name);
                            $printer->text($nameInNotLt . "\n");
                        }
                    }
                }
                $printer->feed();
                $printer->setJustification();
                // change font
                $printer-> setFont(1);
                $printer->setTextSize(2, 2);
                $printer->text($order->amount . ' vnt.' . "\n");
                $printer->feed();

                $printer->setTextSize(2, 1);
                $takeaway = $order->takeaway ? 'Vietoje' : 'Išsinešimui';
                $printer->text($takeaway . "\n");
                // change font
                $printer-> setFont(0);
                $printer->setTextSize(2, 1);

                if ($order->comments) {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->text('Pageidavimai: ' . "\n");
                    $printer->text($order->comments . "\n");
                    $printer->feed();
                }
                $printer->text(str_repeat("-", 24) . "\n"); // Adjust 48 to match your printer's width
                $printer->feed();
            }
            $printer ->feed(4);
            $printer->cut();
            /* Close printer */
            $printer->close();
        }
        catch
        (Exception $e) {
            echo "Couldn't print to kitchen this printer: " . $e->getMessage() . "\n";
        }
    }

    private function printOrderForClient(int $freeOrder): void
    {
        try {
            $profile = CapabilityProfile::load("TM-P80");
            $connector = new NetworkPrintConnector("192.168.1.101", 9100, 2);
            $printer = new Printer($connector, $profile);
            $printer -> setEmphasis(true);

            $printer->feed();
            $printer->setTextSize(6, 5);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($freeOrder . "\n");
            // change font
            $printer-> setFont(1);
            $printer->setTextSize(2, 1);
            $printer->text("\n" . now('Europe/Vilnius') . "\n");
            $printer->text(str_repeat("-", 24) . "\n"); // Adjust 48 to match your printer's width*/
            $printer->feed();

            $printer ->text("Nefiskalinis kvitas \n");
            $printer -> feed(2);

            $totalSum = Cache::get('totalSum');
            $printer -> text('Viso ' . $totalSum . ' EUR' . "\n");

            $printer ->feed(4);
            $printer->cut();
            $printer->close();
        }
        catch
        (Exception $e) {
            echo "Couldn't print to client printer: " . $e->getMessage() . "\n";
        }
    }
    private function convertTextFromLtToNotLt($text)
    {
        $search = ["Č", "ė", "ū", "ų", "š", "ž", "ą", "ė", "į", "ė", "ą", "ė", "į", "ū", "ų", "č", "ė", "ū", "ų"];
        $replace = ["C", "e", "u", "u", "s", "z", "a", "e", "i", "e", "a", "e", "i", "u", "u", "c", "e", "u", "u"];
        return str_replace($search, $replace, $text);
    }
    public function mount()
    {
        $this->byPhone = false;
        $this->orders = Order::orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.orders.orders-list');
    }
}
