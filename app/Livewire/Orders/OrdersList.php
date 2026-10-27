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
use App\Printing\TimedNetworkPrintConnector;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mike42\Escpos\Printer;

class OrdersList extends Component
{
    public $orders = [];

    public $toppings = [];

    public int $freeNumber;

    public bool $byPhone;

    public bool $koreguoti;

    /**
     * Printers that didn't print the last checkout, shown to the cashier with a retry button.
     *
     * @var list<string>
     */
    public array $unreachablePrinters = [];

    /** Cache key of the receipts that didn't print, kept for the retry button. */
    public ?string $failedReceiptsKey = null;

    /** The order number taken by the current checkout, for the link in its receipt QR code. */
    private ?OrderNumbers $takenOrder = null;

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

        $this->printCheckoutReceipts($orderNumber, $this->byPhone, $this->takenOrder?->statusUrl());
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
        $this->takenOrder = null;

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
        $this->takenOrder = OrderNumbers::create([
            'number' => $this->freeNumber,
            'by_phone' => $this->byPhone,
            'created_at' => now(),
        ]);
        FreeNumbers::first()->update(['number' => $this->freeNumber + 1]);
        $this->saveKitchenItems($this->takenOrder);

        return $this->freeNumber;
    }

    /**
     * Copies the kitchen lines of the cart onto the order number, so the kitchen screen still knows
     * what to make after the cart is cleared. A failure here never stops the checkout.
     */
    private function saveKitchenItems(OrderNumbers $orderNumber): void
    {
        try {
            $this->copyKitchenItems($orderNumber);
        } catch (QueryException $e) {
            Log::warning("Couldn't save the kitchen items of order {$orderNumber->number}: ".$e->getMessage());
        }
    }

    private function copyKitchenItems(OrderNumbers $orderNumber): void
    {
        $productNames = fn (?string $json): array => collect(json_decode($json ?? '[]', true) ?? [])
            ->flatMap(fn (array $product): array => array_keys($product))
            ->map(fn ($name): string => (string) $name)
            ->all();

        foreach (Order::where('for_kitchen', true)->orderBy('category')->orderBy('id')->get() as $order) {
            foreach ($productNames($order->name) as $name) {
                $orderNumber->kitchenItems()->create([
                    'name' => $name,
                    'amount' => $order->amount,
                    'toppings' => $productNames($order->toppings),
                    'category' => $order->category,
                    'takeaway' => (bool) $order->takeaway,
                    'comments' => $order->comments,
                ]);
            }
        }
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

    public function shouldPrintForKitchen(): bool
    {
        return Order::where('for_kitchen', true)->exists();
    }

    private const PRINTER_NAMES = [
        'kitchen' => 'virtuvės',
        'client' => 'kliento',
    ];

    /**
     * Which client receipt copies to print: a branded one for the customer and a plain one for the till,
     * or only the plain one when the order came by phone and nobody takes a receipt at the counter.
     *
     * @return list<bool> The `branded` flag of every copy, in print order.
     */
    public function clientReceiptCopies(bool $byPhone): array
    {
        return $byPhone ? [false] : [true, false];
    }

    /** About 2 cm of blank paper above the kitchen receipt (203 dpi). */
    public const KITCHEN_TOP_MARGIN = 160;

    public const KITCHEN_ITEM_SIZE = 30;

    /**
     * The kitchen receipt: the order number and time on top, then every kitchen item in an amount column
     * with its toppings, takeaway mark and wishes indented under the name.
     */
    public function kitchenReceipt(int $orderNumber, bool $byPhone): ReceiptCanvas
    {
        $font = fn (string $name): string => resource_path("fonts/{$name}.ttf");
        $text = fn (string $text, string $fontName, float $size, string $align, int $baseline): array => ['text' => $text, 'font' => $font($fontName), 'size' => $size, 'align' => $align, 'baseline' => $baseline];
        $itemFonts = ['amount' => $font('Montserrat-Bold'), 'name' => $font('Montserrat-Bold'), 'price' => $font('Montserrat-Bold')];
        $now = now('Europe/Vilnius');

        $receipt = (new ReceiptCanvas($font('Inter-Medium')))
            ->space(self::KITCHEN_TOP_MARGIN)
            ->group(112, [
                $text('UŽSAKYMAS Nr.', 'Montserrat-Bold', 14, 'left', 20),
                $text((string) $orderNumber, 'Montserrat-Bold', 72, 'left', 102),
                $text($now->format('H:i'), 'Montserrat-Bold', 34, 'right', 56),
                $text($byPhone ? 'TELEFONU' : $now->format('Y-m-d'), 'Montserrat-Bold', 18, 'right', 92),
            ])
            ->separator(thickness: 3);

        foreach (Order::where('for_kitchen', true)->orderBy('category')->orderBy('id')->get() as $index => $order) {
            if ($index > 0) {
                $receipt->separator(dashed: true, thickness: 3, dash: 8, gap: 4);
            }

            foreach (json_decode($order->name, true) ?? [] as $product) {
                foreach ($product as $name => $price) {
                    $receipt->line($order->amount.'×', $name, '', self::KITCHEN_ITEM_SIZE, 62, $itemFonts);
                }
            }

            foreach (json_decode($order->toppings ?? '[]', true) ?? [] as $topping) {
                foreach ($topping as $name => $price) {
                    $receipt->line('', '+ '.$name, '', 26, 62, $itemFonts);
                }
            }

            $receipt->line('', $order->takeaway ? '» IŠSINEŠIMUI' : '» Vietoje', '', 24, 62, $itemFonts);

            if ($order->comments) {
                $receipt->line('', '! '.$order->comments, '', 24, 62, $itemFonts);
            }
        }

        return $receipt->separator(thickness: 3);
    }

    /**
     * What the QR code on a numbered client receipt is for, printed right above it.
     *
     * @var array{0: string, 1: string}
     */
    public const QR_CAPTION = ['Sekite užsakymą telefonu', 'Nuskenuokite – pranešime, kai bus paruoštas'];

    /**
     * @param  ?string  $statusUrl  Personal page of the order for the QR code; the general board when the order has no number.
     */
    /**
     * The client receipt; the unbranded second copy starts after a 2 cm margin and leaves out the kibinas,
     * the name, the thank-you line and the QR code.
     */
    public function fullClientReceipt(?int $orderNumber, bool $byPhone, ?string $statusUrl = null, bool $branded = true): ReceiptCanvas
    {
        $font = fn (string $name): string => resource_path("fonts/{$name}.ttf");
        $text = fn (string $text, string $fontName, float $size, string $align, int $baseline): array => ['text' => $text, 'font' => $font($fontName), 'size' => $size, 'align' => $align, 'baseline' => $baseline];
        $now = now('Europe/Vilnius');
        $statusUrl ??= 'https://'.config('online-orders.public_host').'/online';

        $brandHeight = $branded ? 80 : 0;
        $receipt = new ReceiptCanvas($font('Inter-Regular'));

        $branded
            ? $receipt->space(4)->image(resource_path('images/receipt-kibinas.png'))
            : $receipt->space(160);

        $receipt->group(($orderNumber === null ? 54 : 70) + $brandHeight, array_values(array_filter([
            $branded ? $text('UŽEIGA', 'Montserrat-Bold', 42.7, 'center', 53) : null,
            $branded ? $text('Prusakovų', 'Montserrat-Regular', 17.2, 'center', 77) : null,
            $text($orderNumber === null ? '' : 'UŽSAKYMAS Nr.', 'Montserrat-Bold', 12, 'left', 18 + $brandHeight),
            $text($orderNumber === null ? '' : (string) $orderNumber, 'Montserrat-Bold', 40, 'left', 60 + $brandHeight),
            $text($now->format('Y-m-d  H:i'), 'Inter-Medium', 16, 'right', ($orderNumber === null ? 26 : 32) + $brandHeight),
            $text('Nefiskalinis kvitas', 'Inter-Medium', 15, 'right', ($orderNumber === null ? 49 : 59) + $brandHeight),
        ])))
            ->separator(dashed: true, thickness: 2, dash: 6, gap: 3)
            ->space(1);

        $lines = $this->fullReceiptLines();
        $total = array_pop($lines);
        $itemFonts = ['amount' => $font('Montserrat-Regular'), 'name' => $font('Inter-Medium'), 'price' => $font('Montserrat-Regular')];

        foreach ($lines as $index => $line) {
            if (preg_match('/^(\d+) x (.+)$/su', $line['text'], $item)) {
                $receipt->space($index === 0 ? 0 : 7)->line($item[1].'×', $item[2], $line['price'] ?? '', 21, 51, $itemFonts);

                continue;
            }

            $receipt->line('', '•  '.$line['text'], $line['price'] ?? '', 18, 60, ['amount' => $font('Inter-Medium'), 'name' => $font('Inter-Medium'), 'price' => $font('Montserrat-Regular')]);
        }

        $receipt->space(8)
            ->separator(thickness: 3)
            ->space(-9)
            ->separator(thickness: 2)
            ->group(48, [
                $text('VISO', 'Montserrat-Bold', 20, 'left', 38),
                $text(str_replace(' EUR', ' €', $total['price']), 'Montserrat-Bold', 26, 'right', 39),
            ])
            ->separator(thickness: 2)
            ->group(29, [
                $text($this->itemCountLabel((int) Order::sum('amount')), 'Inter-Medium', 16, 'left', 22),
                $text($byPhone ? 'Užsakyta: TELEFONU' : '', 'Inter-Medium', 16, 'right', 22),
            ])
            ->separator(dashed: true, thickness: 2, dash: 6, gap: 3);

        if ($branded) {
            $receipt->group(50, [
                $text('Ačiū ir skanaus!', 'Montserrat-Regular', 21, 'center', 37),
            ]);

            if ($orderNumber !== null) {
                [$title, $hint] = self::QR_CAPTION;
                $receipt->space(6)->group(56, [
                    $text($title, 'Montserrat-Bold', 18, 'center', 22),
                    $text($hint, 'Inter-Medium', 15, 'center', 48),
                ]);
            }

            $receipt->qrCode($statusUrl)
                ->group(32, [
                    $text(Str::after($statusUrl, '://'), 'Inter-Medium', 11.5, 'center', 16),
                ]);
        }

        return $receipt;
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

    /**
     * Prints the kitchen receipt and the client copies after any receipts still waiting from earlier,
     * skipping further copies on a printer that already failed, and keeps whatever didn't print for a retry.
     */
    private function printCheckoutReceipts(?int $orderNumber, bool $byPhone, ?string $statusUrl): void
    {
        $receipts = $this->failedReceiptsKey !== null ? Cache::get($this->failedReceiptsKey, []) : [];

        if ($orderNumber !== null) {
            $receipts[] = ['printer' => 'kitchen', 'data' => $this->receiptBytes($this->kitchenReceipt($orderNumber, $byPhone))];
        }
        foreach ($this->clientReceiptCopies($byPhone) as $branded) {
            $receipts[] = ['printer' => 'client', 'data' => $this->receiptBytes($this->fullClientReceipt($orderNumber, $byPhone, $statusUrl, branded: $branded))];
        }

        $this->forgetFailedReceipts();
        $this->printReceipts($receipts);
    }

    /**
     * Sends the cashier's failed receipts to their printers again.
     */
    public function retryFailedReceipts(): void
    {
        $receipts = $this->failedReceiptsKey !== null ? Cache::get($this->failedReceiptsKey, []) : [];

        $this->forgetFailedReceipts();
        $this->printReceipts($receipts);
    }

    public function dismissPrinterWarning(): void
    {
        $this->forgetFailedReceipts();
    }

    /**
     * The warning shown to the cashier when a printer couldn't print.
     */
    public function printerWarning(): ?string
    {
        $names = array_map(fn (string $printer): string => self::PRINTER_NAMES[$printer], $this->unreachablePrinters);

        return match (count($names)) {
            0 => null,
            1 => ucfirst($names[0]).' spausdintuvas nepasiekiamas',
            default => ucfirst(implode(' ir ', $names)).' spausdintuvai nepasiekiami',
        };
    }

    /**
     * @param  list<array{printer: string, data: string}>  $receipts
     */
    private function printReceipts(array $receipts): void
    {
        $failed = [];
        $unreachable = [];

        foreach ($receipts as $receipt) {
            $printer = $receipt['printer'];

            if (isset($unreachable[$printer]) || ! $this->sendToPrinter(config("printers.{$printer}_ip"), $receipt['data'])) {
                $unreachable[$printer] = true;
                $failed[] = $receipt;
            }
        }

        if ($failed === []) {
            return;
        }

        $this->failedReceiptsKey = 'failed-receipts:'.Str::uuid();
        Cache::put($this->failedReceiptsKey, $failed, now()->addHours(2));
        $this->unreachablePrinters = array_keys($unreachable);
    }

    private function forgetFailedReceipts(): void
    {
        if ($this->failedReceiptsKey !== null) {
            Cache::forget($this->failedReceiptsKey);
        }
        $this->failedReceiptsKey = null;
        $this->unreachablePrinters = [];
    }

    /**
     * The receipt as printer commands, ready to send or to keep for a retry.
     * Base64 keeps the raw bytes safe in the database cache.
     */
    public function receiptBytes(ReceiptCanvas $receipt): string
    {
        $connector = new DummyPrintConnector;
        $printer = new Printer($connector);
        $receipt->print($printer);
        $printer->feed(2);
        $printer->cut();
        $data = $connector->getData();
        $printer->close();

        return base64_encode($data);
    }

    private function sendToPrinter(string $printerIp, string $data): bool
    {
        try {
            $connector = new TimedNetworkPrintConnector($printerIp, config('printers.port'), config('printers.timeout'));
        } catch (Exception $e) {
            Log::warning("Couldn't print to printer {$printerIp}: ".$e->getMessage());

            return false;
        }

        try {
            $connector->write(base64_decode($data));

            return true;
        } catch (Exception $e) {
            Log::warning("Couldn't print to printer {$printerIp}: ".$e->getMessage());

            return false;
        } finally {
            $connector->finalize();
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
