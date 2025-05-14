<div class="bg-white/80 flex flex-col  p-1 rounded-xl backdrop-blur-[2px] " xmlns:flux="http://www.w3.org/1999/html">
    <div class="flex items-center justify-between ">
        <h2 class="text-[#191919] text-xs font-medium leading-[30px]">Krepšelis ({{\App\Models\Order::all()->sum('amount')}})</h2>
        <div class="flex items-right gap-2 ">
            <flux:field variant="inline">
                <flux:checkbox wire:model="byPhone" class=" scale-100" />
                <flux:label  class="!text-[#191919] !text-xs font-medium" >Užsakomas telefonu</flux:label>
                <flux:error name="terms" />
            </flux:field>
        </div>
    </div>

    <livewire:orders.category-sum/>
    <div class="">
        <div class=" flex justify-between items-center">
            <div class="font-semibold text-md  text-[#191919]">Iš viso</div>
            <div class="font-semibold text-md text-[#191919]"><livewire:orders.total-sum-manager/></div>
        </div>
    </div>

    <hr class="my-1 h-px border-t-0 bg-transparent bg-gradient-to-r from-transparent via-green-500 " />

    <div class="grid md:grid-cols-3 auto-rows-min gap-1">
        <button wire:click="makeOrder"
                class="bg-emerald-700 rounded-xl text-white/80 text-base font-semibold  leading-tight">
            Užsakyti
        </button>

        <button wire:click="resetOrders"
                class="bg-emerald-600/10 rounded-xl   text-emerald-700 text-base font-semibold  leading-tight">
            Valyti
        </button>

        <flux:modal.trigger name="see-orders" >
            <flux:button class="!text-base !font-semibold !text-white/80 bg-emerald-600/10 !rounded-xl">Užsakymai
            </flux:button>
        </flux:modal.trigger>

        <flux:modal name="see-orders" variant="flyout" class="bg-yellow-300" >
            <livewire:kasa-orders/>
        </flux:modal>
    </div>

    @foreach($orders as $order)

        <div class="bg-white/60 shadow-xl hover:shadow-2xl rounded-xl p-2 gap-1 mb-1 items-center">
            <div class="flex flex-1">
                <div class="auto-rows-min w-7/10" wire:click="removeOrder({{$order->id}})">
                    @foreach(json_decode($order->name) as $name)
                        @foreach($name as $name => $price)
                            <h3 class="  text-[#191919] text-xs">{{ $name }} <span
                                    class="relative justify-start text-[#191919] text-xs font-semibold "> {{ $price }} €</span>
                                @if($order->package)
                                    <span class="relative justify-start text-[#191919] text-xs font-semibold  leading-[16.80px]">
                                        + 0.30 €</span>
                                @endif
                            </h3>
                        @endforeach
                    @endforeach

                    @if(isset($order->toppings))
                        @foreach(json_decode($order->toppings) as $topping)
                            @foreach($topping as $name => $toppingPrice)
                                <p class="h-4">
                                    <span
                                        class="relative justify-start text-[#7f7f7f] text-xs font-normal  leading-[21px]">{{ $name }}</span><span
                                        class="relative justify-start text-[#191919] text-xs font-semibold  leading-[16.80px]"> {{ $toppingPrice }} €</span>
                                </p>
                            @endforeach
                        @endforeach
                    @endif
                        <p>
                    @if ($order->package)

                                    <span
                                        class="relative justify-start text-[#7f7f7f] text-xs  text-center text-gray-950 font-bold leading-[21px]">Pilna suma:</span><span
                                class="relative justify-start text-[#191919] text-xs font-semibold  leading-[16.80px]"> {{ $order->order_price  + 0.30 }} € &times; {{ $order->amount }} = {{ ($order->order_price + 0.30) * $order->amount }} €</span>


                    @else
                                    <span
                                        class="relative justify-start text-[#7f7f7f] text-xs   text-center text-gray-950 font-bold leading-[21px]">Pilna suma:</span><span
                                class="relative justify-start text-[#191919] text-xs font-semibold  leading-[16.80px]"> {{ $price}} € &times; {{ $order->amount }} = {{ $price * $order->amount }} €</span>
                    @endif
                        <div
                            class=" justify-start text-[#7f7f7f] text-xs   text-left
                                        text-gray-950 font-bold leading-[21px]">{{ $order->takeaway ? "Išsinešimui!" : 'Vietoje!' }}
                        </div>


                        </p>
                </div>
                <div class="auto-rows-min w-3/10 flex items-center gap-1 justify-end">
                    <button wire:click="removeOneOrder({{$order->id}})" class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-700 hover:bg-gray-500 focus:outline-none">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </button>

                    <span  class="text-lg  m-1 text-center text-gray-950 font-bold ">{{ $order->amount }}</span>

                    <button wire:click="addOneOrder({{$order->id}})" class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-600 hover:bg-emerald-700 focus:outline-none">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12M6 12h12"></path>
                        </svg>
                    </button>
                    <flux:modal.trigger  class="flex items-center justify-center" wire:click="openCommentForOrder({{ $order->id }})" name="add-comment-for-order-{{$order->id}}">
                        <button class="flex items-center justify-center ml-2 w-8 h-8 rounded-full text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
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
