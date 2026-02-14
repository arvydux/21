<div class="flex flex-col p-2 rounded-2xl"
     style="background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);"
     xmlns:flux="http://www.w3.org/1999/html">
    <div class="flex items-center justify-between ">
        <h2 class="text-white text-xs font-semibold leading-[30px] tracking-wide">Krepšelis ({{\App\Models\Order::all()->sum('amount')}})</h2>
        <div class="flex items-right gap-2 ">
            <flux:field variant="inline">
                <flux:checkbox wire:model="byPhone" class=" scale-140" />
                <flux:label  class="!text-white/80 !text-md font-medium" >Užsakomas telefonu</flux:label>
                <flux:error name="terms" />
            </flux:field>
        </div>
    </div>

    <livewire:orders.category-sum/>
    <div class="">
        <div class=" flex justify-between items-center">
            <div class="font-extrabold text-md text-emerald-400">Iš viso</div>
            <div class="font-extrabold text-md text-emerald-400"><livewire:orders.total-sum-manager/></div>
        </div>
    </div>

    <hr class="my-1 h-px border-t-0 bg-transparent bg-gradient-to-r from-transparent via-white/30 to-transparent" />

    <div class="grid md:grid-cols-3 auto-rows-min gap-1">
        <button wire:click="makeOrder"
                class="rounded-xl text-white text-base font-bold leading-tight transition-all duration-200 hover:scale-[1.03] active:scale-[0.97]"
                style="background: rgba(16, 185, 129, 0.5); border: 1px solid rgba(16, 185, 129, 0.5); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);">
            Užsakyti
        </button>

        <button wire:click="resetOrders"
                class="bg-black/25 border border-white/15 rounded-xl text-white text-base font-bold leading-tight transition-all duration-200 hover:bg-black/35 hover:scale-[1.03] active:scale-[0.97]">
            Valyti
        </button>

        <flux:modal.trigger name="see-orders" >
            <flux:button class="!text-base !font-bold !bg-black/25 !border !border-white/15 !rounded-xl !text-white hover:!bg-black/35">Užsakymai
            </flux:button>
        </flux:modal.trigger>

        <flux:modal name="see-orders" variant="flyout" class="bg-yellow-300" >
            <livewire:kasa-orders/>
        </flux:modal>
    </div>

    @foreach($orders as $order)

        <div class="rounded-xl p-2 gap-1 mb-1 items-center border border-emerald-200/30 transition-all duration-200 hover:shadow-lg" style="background: rgba(255, 255, 255, 0.85); box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);" wire:key="order-{{ $order->id }}">
            <div class="flex flex-1">
                <div class="auto-rows-min w-7/10" wire:click="changeTakeaway({{$order->id}})">
                    @foreach(json_decode($order->name) as $name)
                        @foreach($name as $name => $price)
                            <h3 class="text-emerald-900 text-xs">{{ $name }} <span
                                    class="relative justify-start text-emerald-900 text-xs font-semibold"> {{ $price }} €</span>
                                @if($order->package)
                                    <span class="relative justify-start text-emerald-900 text-xs font-semibold leading-[16.80px]">
                                        + {{$order->package}} €</span>
                                @endif
                            </h3>
                        @endforeach
                    @endforeach

                    @if(isset($order->toppings))
                        @foreach(json_decode($order->toppings) as $topping)
                            @foreach($topping as $name => $toppingPrice)
                                <p class="h-4">
                                    <span
                                        class="relative justify-start text-emerald-700/60 text-xs font-normal leading-[21px]">{{ $name }}</span><span
                                        class="relative justify-start text-emerald-900 text-xs font-semibold leading-[16.80px]"> {{ $toppingPrice }} €</span>
                                </p>
                            @endforeach
                        @endforeach
                    @endif
                    <p>
                        @if ($order->package)

                            <span
                                class="relative justify-start text-emerald-700/60 text-xs text-center font-bold leading-[21px]">Pilna suma:</span><span
                                class="relative justify-start text-emerald-900 text-xs font-semibold leading-[16.80px]"> {{ $order->order_price  + $order->package }} € &times; {{ $order->amount }} = {{ ($order->order_price + $order->package) * $order->amount }} €</span>


                        @else
                            <span
                                class="relative justify-start text-emerald-700/60 text-xs text-center font-bold leading-[21px]">Pilna suma:</span><span
                                class="relative justify-start text-emerald-900 text-xs font-semibold leading-[16.80px]"> {{ $price}} € &times; {{ $order->amount }} = {{ $price * $order->amount }} €</span>
                    @endif
                    <div
                        class="justify-start text-emerald-600 text-xs text-left font-bold leading-[21px]">{{ $order->takeaway ? "Išsinešimui!" : 'Vietoje!' }}
                    </div>


                    </p>
                </div>
                <div class="auto-rows-min w-3/10 flex items-center gap-1 justify-end">
                    <button wire:click="removeOneOrder({{$order->id}})" class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100 hover:bg-emerald-200 border border-emerald-200 transition-all duration-200 focus:outline-none text-emerald-700">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </button>

                    <span class="text-2xl m-1 text-center text-emerald-900 font-bold">{{ $order->amount }}</span>

                    <button wire:click="addOneOrder({{$order->id}})" class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-600 hover:bg-emerald-700 border border-emerald-500 transition-all duration-200 focus:outline-none text-white">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12M6 12h12"></path>
                        </svg>
                    </button>
                    <flux:modal.trigger  class="flex items-center justify-center" wire:click="openCommentForOrder({{ $order->id }})" name="add-comment-for-order-{{$order->id}}">
                        <button class="flex items-center justify-center ml-2 w-10 h-10 rounded-full text-white bg-emerald-600 hover:bg-emerald-700 border border-emerald-500 transition-all duration-200 focus:outline-none">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8-1.657 0-3.204-.402-4.5-1.1L3 19l1.1-3.5C3.402 14.204 3 12.657 3 11c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </button>
                    </flux:modal.trigger>

                    <flux:modal name="add-comment-for-order-{{ $order->id }}" class="w-full">
                        <div class="space-y-6">
                            <div>
                                @foreach(json_decode($order->name, true) as $name)
                                    @foreach($name as $key => $value)
                                    @endforeach
                                @endforeach
                                <flux:heading size="lg">{{ $key }}</flux:heading>
                            </div>

                            <flux:textarea label="Komentarai bei pageidavimai." wire:model="comments" />

                            <div class="flex">
                                <flux:spacer />

                                <flux:button wire:click="makeCommentForOrder({{ $order->id }})" x-on:click="$flux.modals().close()"
                                             type="submit" variant="primary">Pridėti komentarus</flux:button>
                            </div>
                        </div>
                    </flux:modal>
                </div>
            </div>


        </div>

    @endforeach
</div>


<script>


    function fetchReadyOrders() {
        fetch('/get-ready-orders')
            .then(response => response.json())
            .then(data => {
                const ordersDiv = document.getElementById('ready-orders');
                ordersDiv.innerHTML =``;
                data.forEach(order => {
                    const orderHTML = `<div wire:click="makeOrderTaken('${order.number}')"  class="relative text-center shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
                aspect-video overflow-hidden rounded-xl  bg-emerald-600/80  dark:border-neutral-700">
                <div class="flex grid content-center flex-col gap-2 h-full text-white/80 rounded-xl w-full">
                    <div class="font-semibold text-8xl">${order.number}</div>
                </div>
            </div> `;
                    ordersDiv.insertAdjacentHTML('beforeend', orderHTML);
                });
            });
    }
    // Initial fetch
    //  fetchUnreadyOrders();
    fetchReadyOrders();

    // Fetch orders every 5 seconds
    // setInterval(fetchUnreadyOrders, 5000);
    setInterval(fetchReadyOrders, 5000);


</script>

