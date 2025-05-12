<?php

namespace App\Livewire\Orders;

use App\Models\FreeNumbers;
use App\Models\Order;
use App\Models\OrderNumbers;
use Exception;
use Livewire\Attributes\On;
use Livewire\Component;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
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

        return $this->freeNumber;
    }

    private function printOrderForKitchen(int $freeOrder): void
    {
        try {
            $profile = CapabilityProfile::load(config('printers.profile_name'));
            $connector = new NetworkPrintConnector(
                config('printers.kitchen_printer.kitchen_printer_ip'),
                config('printers.kitchen_printer.kitchen_printer_port')
            );
            $printer = new Printer($connector, $profile);
            $printer -> setEmphasis(true);
            $printer->setTextSize(4, 4);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($freeOrder . "\n");
            $printer->setTextSize(2, 1);
            $printer->text("\n" . now('Europe/Vilnius') . "\n");
            $printer->text(str_repeat("-", 24) . "\n"); // Adjust 48 to match your printer's width
            foreach (Order::where('category', 1)->orwhere('category', 4)->orderBy('category')->get() as $order) {
                foreach (json_decode($order->name) as $name) {
                    foreach ($name as $name => $price) {
                        $nameInNotLt = $this->convertTextFromLtToNotLt($name);
                        $printer->setJustification();
                        $printer -> setEmphasis(true);
                        $printer->setJustification();
                        $printer->text($nameInNotLt . "\n");
                    }
                }

                if (isset($order->toppings)) {
                    $printer->feed();
                    foreach (json_decode($order->toppings) as $topping) {
                        foreach ($topping as  $name => $toppingPrice) {
                            $printer->setJustification(Printer::JUSTIFY_RIGHT);
                            $printer->setTextSize(2, 1);
                            $nameInNotLt = $this->convertTextFromLtToNotLt($name);
                            $printer->text($nameInNotLt . "\n");
                        }
                    }
                }
                $printer->feed();
                $printer->setJustification();
                $printer->setTextSize(2, 1);
                $printer->text($order->amount . ' vnt.' . "\n");
                $takeaway = $order->takeaway ? 'Vietoje' : 'Išsinešimui';
                $printer->text($takeaway . "\n");
                $printer->feed();
                  if ($order->comments) {
                      $printer->setJustification(Printer::JUSTIFY_CENTER);
                      $printer->text('Pageidavimai: ' . "\n");
                      $printer->text($order->comments . "\n");
                      $printer->feed();
                  }
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
            $connector = new WindowsPrintConnector("smb://" . config('printers.client_printer.computer_printer_name') . "/" . config('printers.client_printer.printer_for_client_name'));
            $printer = new Printer($connector);

            $printer->feed(2);
            $printer->setTextSize(8, 8);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($freeOrder . "\n");
            $printer->setTextSize(2, 1);
            $printer->text("\n" . now('Europe/Vilnius') . "\n");
            $printer->text(str_repeat("-", 24) . "\n"); // Adjust 48 to match your printer's width*/
            $printer->feed();

            $printer->setTextSize(2, 2);
            $totalSum = Cache::get('totalSum');
            $printer -> text('Viso ' . $totalSum . ' EUR' . "\n");
            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);            $printer -> feed();
            $printer ->text("Nefiskalinis kvitas \n");
            $printer ->feed(2);
            $printer->cut();
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
        $this->orders = Order::orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.orders.orders-list');
    }
}
