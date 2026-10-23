<?php

namespace App\Livewire\Orders;

use App\Models\AlcoholicDrink;
use App\Models\Ceburek;
use App\Models\Drink;
use App\Models\FreeNumbers;
use App\Models\Kepiniai;
use App\Models\KoldunaiDish;
use App\Models\KoldunaiTopping;
use App\Models\Order;
use App\Models\OrderNumbers;
use App\Models\OtherProduct;
use App\Models\PotatoDish;
use App\Models\PotatoTopping;
use App\Models\Topping;
use App\Printing\ReceiptCanvas;
use Exception;
use Livewire\Attributes\On;
use Livewire\Component;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

class OrdersList extends Component
{
    public $orders = [];

    public $toppings = [];

    public int $freeNumber;

    public bool $byPhone;

    public bool $koreguoti;

    #[On('toggle-koreguoti')]
    public function toggleKoreguoti(): void
    {
        $this->koreguoti = ! $this->koreguoti;
        $this->dispatch('koreguoti-changed', active: $this->koreguoti);
    }

    public $comments;

    private int $limit = 100;

    #[on('change-order')]
    public function fetchOrders()
    {
        $this->orders = Order::orderBy('created_at', 'desc')->get();
        $this->dispatch('cart-updated', counts: $this->cartCounts());
    }

    /**
     * Amount in the cart of every ordered product name, used for the badges on the product buttons.
     *
     * @return array<string, int>
     */
    public function cartCounts(): array
    {
        $counts = [];
        foreach ($this->orders as $order) {
            foreach (json_decode($order->name, true) ?? [] as $product) {
                foreach (array_keys($product) as $name) {
                    $counts[$name] = ($counts[$name] ?? 0) + $order->amount;
                }
            }
        }

        return $counts;
    }

    /**
     * Price of one unit of the cart line: product, toppings and package.
     */
    public function unitPrice(Order $order): float
    {
        $price = 0.0;
        foreach ([$order->name, $order->toppings ?? '[]'] as $json) {
            foreach (json_decode($json, true) ?? [] as $item) {
                $price += array_sum(array_map('floatval', $item));
            }
        }

        return $price + (float) $order->package;
    }

    public function itemCountLabel(int $count): string
    {
        $word = match (true) {
            $count % 10 === 1 && $count % 100 !== 11 => 'prekė',
            $count % 10 === 0 || ($count % 100 >= 11 && $count % 100 <= 19) => 'prekių',
            default => 'prekės',
        };

        return $count.' '.$word;
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

    public function removeOneOrder($id): void
    {
        $order = Order::find($id);
        if ($order !== null) {
            $order->amount -= 1;
            $order->save();
            if ($order->amount === 0) {
                $this->removeOrder($id);
            }
            $this->dispatch('change-order');
        }
    }

    public function removeOrder($id): void
    {
        $order = Order::find($id);
        if ($order !== null) {
            $order->delete();
            $this->dispatch('change-order');
        }
    }

    public function changeTakeaway($id): void
    {
        $order = Order::find($id);
        if ($order) {
            $order->takeaway = ! $order->takeaway;
            $order->save();
        }
        $this->dispatch('change-order');
    }

    public function resetOrders()
    {
        Order::truncate();
        $this->dispatch('change-order');
        $this->dispatch('reset-orders');
    }

    private function makeOrderNumber(): ?int
    {
        $orderNumber = $this->takeOrderNumber();

        if ($orderNumber !== null) {
            $this->printOrderForKitchen($orderNumber, $this->byPhone);
        }
        $this->printOrderForClient($orderNumber, $this->byPhone);
        $this->decrementCeburekLeft();
        $this->decrementKepiniaiLeft();
        $this->decrementDrinkLeft();
        $this->decrementOtherProductLeft();
        $this->decrementToppingLeft();
        $this->decrementPotatoDishLeft();
        $this->decrementKoldunaiDishLeft();
        $this->decrementAlcoholicDrinkLeft();
        $this->resetOrders();
        $this->byPhone = false;

        return $orderNumber;
    }

    /**
     * Reserves the next order number, but only when something goes to the kitchen.
     * Orders without kitchen items get no number and leave the counter untouched.
     */
    public function takeOrderNumber(): ?int
    {
        if (! $this->shouldPrintForKitchen()) {
            return null;
        }

        // free number maximum value
        if (FreeNumbers::first()->number >= $this->limit) {
            FreeNumbers::first()->update(['number' => 1]);
            OrderNumbers::where('is_taken', true)
                ->delete();
        }
        $this->freeNumber = FreeNumbers::first()->number;
        OrderNumbers::create([
            'number' => $this->freeNumber,
            'by_phone' => $this->byPhone,
            'created_at' => now(),
        ]);
        FreeNumbers::first()->update(['number' => $this->freeNumber + 1]);

        return $this->freeNumber;
    }

    private function decrementCeburekLeft(): void
    {
        foreach (Order::where('category', 1)->get() as $order) {
            foreach (json_decode($order->name, true) as $item) {
                foreach ($item as $productName => $price) {
                    $ceburek = Ceburek::where('name', $productName)
                        ->whereNotNull('left')
                        ->first();
                    if ($ceburek) {
                        $ceburek->left = max(0, $ceburek->left - $order->amount);
                        $ceburek->save();
                    }
                }
            }
        }
    }

    private function decrementKepiniaiLeft(): void
    {
        foreach (Order::where('category', 2)->get() as $order) {
            foreach (json_decode($order->name, true) as $item) {
                foreach ($item as $productName => $price) {
                    $kepinys = Kepiniai::where('name', $productName)
                        ->whereNotNull('left')
                        ->first();
                    if ($kepinys) {
                        $kepinys->left = max(0, $kepinys->left - $order->amount);
                        $kepinys->save();
                    }
                }
            }
        }
    }

    private function decrementDrinkLeft(): void
    {
        foreach (Order::where('category', 3)->get() as $order) {
            foreach (json_decode($order->name, true) as $item) {
                foreach ($item as $productName => $price) {
                    $drink = Drink::where('name', $productName)
                        ->whereNotNull('left')
                        ->first();
                    if ($drink) {
                        $drink->left = max(0, $drink->left - $order->amount);
                        $drink->save();
                    }
                }
            }
        }
    }

    private function decrementOtherProductLeft(): void
    {
        foreach (Order::where('category', 4)->get() as $order) {
            foreach (json_decode($order->name, true) as $item) {
                foreach ($item as $productName => $price) {
                    $product = OtherProduct::where('name', $productName)
                        ->whereNotNull('left')
                        ->first();
                    if ($product) {
                        $product->left = max(0, $product->left - $order->amount);
                        $product->save();
                    }
                }
            }
        }
    }

    private function decrementToppingLeft(): void
    {
        foreach (Order::where('category', 1)->get() as $order) {
            foreach (json_decode($order->toppings, true) as $toppingItem) {
                foreach ($toppingItem as $toppingName => $price) {
                    $topping = Topping::where('name', $toppingName)->whereNotNull('left')->first();
                    if ($topping) {
                        $topping->left = max(0, $topping->left - $order->amount);
                        $topping->save();
                    }
                }
            }
        }
    }

    private function decrementPotatoDishLeft(): void
    {
        foreach (Order::where('category', PotatoDish::ORDER_CATEGORY)->get() as $order) {
            foreach (json_decode($order->name, true) as $item) {
                foreach ($item as $orderLabel => $price) {
                    $product = PotatoDish::where('name', PotatoDish::nameFromOrderLabel($orderLabel))
                        ->whereNotNull('left')
                        ->first();
                    if ($product) {
                        $product->left = max(0, $product->left - $order->amount);
                        $product->save();
                    }
                }
            }
            foreach (json_decode($order->toppings, true) ?? [] as $toppingItem) {
                foreach ($toppingItem as $toppingName => $price) {
                    $topping = PotatoTopping::where('name', $toppingName)->whereNotNull('left')->first();
                    if ($topping) {
                        $topping->left = max(0, $topping->left - $order->amount);
                        $topping->save();
                    }
                }
            }
        }
    }

    private function decrementKoldunaiDishLeft(): void
    {
        foreach (Order::where('category', KoldunaiDish::ORDER_CATEGORY)->get() as $order) {
            foreach (json_decode($order->name, true) as $item) {
                foreach ($item as $productName => $price) {
                    $product = KoldunaiDish::where('name', $productName)->whereNotNull('left')->first();
                    if ($product) {
                        $product->left = max(0, $product->left - $order->amount);
                        $product->save();
                    }
                }
            }
            foreach (json_decode($order->toppings, true) ?? [] as $toppingItem) {
                foreach ($toppingItem as $toppingName => $price) {
                    $topping = KoldunaiTopping::where('name', $toppingName)->whereNotNull('left')->first();
                    if ($topping) {
                        $topping->left = max(0, $topping->left - $order->amount);
                        $topping->save();
                    }
                }
            }
        }
    }

    private function decrementAlcoholicDrinkLeft(): void
    {
        foreach (Order::where('category', AlcoholicDrink::ORDER_CATEGORY)->get() as $order) {
            foreach (json_decode($order->name, true) as $item) {
                foreach ($item as $productName => $price) {
                    $alcoholicDrink = AlcoholicDrink::where('name', $productName)->whereNotNull('left')->first();
                    if ($alcoholicDrink) {
                        $alcoholicDrink->left = max(0, $alcoholicDrink->left - $order->amount);
                        $alcoholicDrink->save();
                    }
                }
            }
        }
    }

    /**
     * @return list<int>
     */
    private function kitchenCategories(): array
    {
        return [1, 4, PotatoDish::ORDER_CATEGORY, KoldunaiDish::ORDER_CATEGORY];
    }

    public function shouldPrintForKitchen(): bool
    {
        return Order::whereIn('category', $this->kitchenCategories())->exists();
    }

    private const KITCHEN_PRINTER_IP = '192.168.32.100';

    private const CLIENT_PRINTER_IP = '192.168.32.101';

    public const CLIENT_RECEIPT_COPIES = 2;

    public function kitchenReceipt(int $orderNumber, bool $byPhone): ReceiptCanvas
    {
        $receipt = (new ReceiptCanvas)
            ->text((string) $orderNumber, 64, 'center')
            ->text($this->printedAt(), 16, 'center')
            ->separator();

        foreach (Order::whereIn('category', $this->kitchenCategories())->orderBy('category')->get() as $order) {
            foreach (json_decode($order->name, true) ?? [] as $product) {
                foreach ($product as $name => $price) {
                    $receipt->text($order->amount.' - '.$name, 24);
                }
            }

            foreach (json_decode($order->toppings ?? '[]', true) ?? [] as $topping) {
                foreach ($topping as $name => $price) {
                    $receipt->text($name, 24, 'right');
                }
            }

            $receipt->text($order->takeaway ? 'Išsinešimui' : 'Vietoje', 24);

            if ($order->comments) {
                $receipt->text('Pageidavimai:', 18, 'center')
                    ->text($order->comments, 18, 'center');
            }
            $receipt->separator();
        }

        if ($byPhone) {
            $receipt->text('Telefonu', 28, 'center');
        }

        return $receipt;
    }

    public function fullClientReceipt(?int $orderNumber, bool $byPhone): ReceiptCanvas
    {
        $font = fn (string $name): string => resource_path("fonts/{$name}.ttf");
        $text = fn (string $text, string $fontName, float $size, string $align, int $baseline): array => ['text' => $text, 'font' => $font($fontName), 'size' => $size, 'align' => $align, 'baseline' => $baseline];
        $now = now('Europe/Vilnius');

        $receipt = (new ReceiptCanvas($font('Inter-Regular')))
            ->space(4)
            ->image(resource_path('images/receipt-kibinas.png'))
            ->group($orderNumber === null ? 112 : 150, [
                $text('UŽEIGA', 'Montserrat-Bold', 42.7, 'center', 53),
                $text('Prusakovų', 'Montserrat-Regular', 17.2, 'center', 77),
                $text($orderNumber === null ? '' : 'UŽSAKYMAS Nr.', 'Montserrat-Regular', 11, 'left', 98),
                $text($orderNumber === null ? '' : (string) $orderNumber, 'Montserrat-Bold', 40, 'left', 140),
                $text($now->format('Y-m-d  H:i'), 'Inter-Medium', 9.5, 'right', $orderNumber === null ? 87 : 120),
                $text('Nefiskalinis kvitas', 'Inter-Regular', 9.7, 'right', $orderNumber === null ? 106 : 139),
            ])
            ->separator(dashed: true, thickness: 2, dash: 6, gap: 3)
            ->space(1);

        $lines = $this->fullReceiptLines();
        $total = array_pop($lines);
        $itemFonts = ['amount' => $font('Montserrat-Light'), 'name' => $font('Inter-Regular'), 'price' => $font('Montserrat-Light')];

        foreach ($lines as $index => $line) {
            if (preg_match('/^(\d+) x (.+)$/su', $line['text'], $item)) {
                $receipt->space($index === 0 ? 0 : 5)->line($item[1].'×', $item[2], $line['price'] ?? '', 12, 33, $itemFonts);

                continue;
            }

            $receipt->line('', '•  '.$line['text'], $line['price'] ?? '', 10, 41, ['amount' => $font('Inter-Regular'), 'name' => $font('Inter-Regular'), 'price' => $font('Montserrat-Light')]);
        }

        return $receipt->space(8)
            ->separator(thickness: 3)
            ->space(-9)
            ->separator(thickness: 2)
            ->group(42, [
                $text('VISO', 'Montserrat-ExtraLight', 18.3, 'left', 35),
                $text(str_replace(' EUR', ' €', $total['price']), 'Montserrat-ExtraLight', 23.6, 'right', 35),
            ])
            ->separator(thickness: 2)
            ->group(17, [
                $text($this->itemCountLabel((int) Order::sum('amount')), 'Inter-Regular', 9.7, 'left', 13),
                $text($byPhone ? 'Užsakyta: TELEFONU' : '', 'Inter-Regular', 9.7, 'right', 13),
            ])
            ->separator(dashed: true, thickness: 2, dash: 6, gap: 3)
            ->group(42, [
                $text('Ačiū ir skanaus!', 'Montserrat-Light', 14.4, 'center', 29),
            ])
            ->image(resource_path('images/receipt-qr.png'))
            ->group(30, [
                $text('status.prusakovuuzeiga.lt/online', 'Inter-Regular', 9.7, 'center', 14),
            ]);
    }

    /**
     * Lines of the second client receipt: every item of the order with its price, then the total.
     *
     * @return list<array{text: string, price: ?string, indent: int}>
     */
    public function fullReceiptLines(): array
    {
        $lines = [];
        $total = 0.0;
        $addLine = function (string $text, ?float $price = null, int $indent = 0) use (&$lines, &$total): void {
            $total += $price ?? 0;
            $lines[] = ['text' => $text, 'price' => $price === null ? null : $this->formatPrice($price), 'indent' => $indent];
        };

        foreach (Order::orderBy('category')->orderBy('id')->get() as $order) {
            foreach (json_decode($order->name, true) ?? [] as $product) {
                foreach ($product as $name => $price) {
                    $addLine($order->amount.' x '.$name, $order->amount * (float) $price);
                }
            }

            foreach (json_decode($order->toppings ?? '[]', true) ?? [] as $topping) {
                foreach ($topping as $name => $price) {
                    $addLine('+ '.$name, (float) $price > 0 ? $order->amount * (float) $price : null, 30);
                }
            }

            if ($order->package) {
                $addLine('+ Pakuotė', $order->amount * (float) $order->package, 30);
            }

            if ($order->takeaway) {
                $addLine('Išsinešimui', indent: 30);
            }

            if ($order->comments) {
                $addLine('Pageidavimai: '.$order->comments, indent: 30);
            }
        }

        $lines[] = ['text' => 'Viso', 'price' => $this->formatPrice($total).' EUR', 'indent' => 0];

        return $lines;
    }

    private function formatPrice(float $price): string
    {
        return number_format($price, 2, '.', '');
    }

    private function printedAt(): string
    {
        return now('Europe/Vilnius')->format('Y-m-d H:i:s');
    }

    private function printOrderForKitchen(int $freeOrder, bool $byPhone): void
    {
        $this->printReceipt(self::KITCHEN_PRINTER_IP, fn () => $this->kitchenReceipt($freeOrder, $byPhone));
    }

    private function printOrderForClient(?int $freeOrder, bool $byPhone): void
    {
        $receipt = $this->fullClientReceipt($freeOrder, $byPhone);

        for ($copy = 0; $copy < self::CLIENT_RECEIPT_COPIES; $copy++) {
            $this->printReceipt(self::CLIENT_PRINTER_IP, fn () => $receipt);
        }
    }

    /**
     * @param  callable(): ReceiptCanvas  $receipt
     */
    private function printReceipt(string $printerIp, callable $receipt): void
    {
        try {
            $printer = new Printer(new NetworkPrintConnector($printerIp, 9100, 2));
            $receipt()->print($printer);
            $printer->feed(2);
            $printer->cut();
            $printer->close();
        } catch (Exception $e) {
            echo "Couldn't print to printer {$printerIp}: ".$e->getMessage()."\n";
        }
    }

    public function mount()
    {
        $this->byPhone = false;
        $this->koreguoti = false;
        $this->orders = Order::orderBy('created_at', 'desc')->get();
        $this->dispatch('cart-updated', counts: $this->cartCounts());
    }

    public function render()
    {
        $orders = collect($this->orders);

        return view('livewire.orders.orders-list', [
            'itemCount' => (int) $orders->sum('amount'),
            'cartTotal' => $orders->sum(fn (Order $order): float => $this->unitPrice($order) * $order->amount),
            'categorySums' => $this->categorySums(),
        ]);
    }

    /**
     * Cart sums shown above the total: food, drinks and packages, leaving out the empty ones.
     *
     * @return array<string, float>
     */
    public function categorySums(): array
    {
        $orders = collect($this->orders);

        return array_filter([
            'Kepiniai' => (float) $orders->where('category', '!=', 3)->sum(fn (Order $order): float => $order->order_price * $order->amount),
            'Gėrimai' => (float) $orders->where('category', 3)->sum(fn (Order $order): float => $order->order_price * $order->amount),
            'Pakuotės' => (float) $orders->sum(fn (Order $order): float => (float) $order->package * $order->amount),
        ], fn (float $sum): bool => $sum > 0);
    }
}
