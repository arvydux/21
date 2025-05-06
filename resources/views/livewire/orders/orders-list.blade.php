<div class="bg-white/80 flex flex-col  p-4 rounded-xl backdrop-blur-[2px] " xmlns:flux="http://www.w3.org/1999/html">
    <div class="flex items-center mb-3 justify-between ">
        <h2 class="text-[#191919] text-xl font-medium leading-[30px]">Krepšelis ({{\App\Models\Order::all()->sum('amount')}})</h2>
        <div class="flex items-right gap-2 ">
            <flux:field variant="inline">
                <flux:checkbox wire:model="byPhone" class="!mt-1 scale-150" />
                <flux:label  class="!text-[#191919] !text-xl font-medium" >Užsakomas telefonu</flux:label>
                <flux:error name="terms" />
            </flux:field>
        </div>
    </div>

    @foreach($orders as $order)
        <div class="flex bg-white/60 shadow-xl hover:shadow-2xl rounded-2xl p-4 gap-2 mb-2 items-center">
            <div class="flex flex-1">
                <div wire:click="removeOrder({{$order->id}})">
                    @foreach(json_decode($order->name) as $name)
                        @foreach($name as $name => $price)
                            <h3 class="  text-[#191919] text-md font-normal  leading-[21px]">{{ $name }} <span
                                    class="relative justify-start text-[#191919] text-sm font-semibold "> {{ $price }} €</span>
                            </h3>
                        @endforeach
                    @endforeach

                    @if(isset($order->toppings))
                        @foreach(json_decode($order->toppings) as $topping)
                            @foreach($topping as $name => $toppingPrice)
                                <p>
                                    <span
                                        class="relative justify-start text-[#7f7f7f] text-sm font-normal  leading-[21px]">{{ $name }}</span><span
                                        class="relative justify-start text-[#191919] text-sm font-semibold  leading-[16.80px]"> {{ $toppingPrice }} €</span>
                                </p>
                            @endforeach
                        @endforeach
                    @endif
                    @if($order->package)
                        <p>
                                    <span
                                        class="relative justify-start text-[#7f7f7f] text-sm font-semibold  leading-[21px]">Pakuotės mokestis</span><span
                                class="relative justify-start text-[#191919] text-sm font-semibold  leading-[16.80px]"> 0.30 €</span>
                        </p>
                    @endif
                    @if ($order->package)
                        <p>
                                    <span
                                        class="relative justify-start text-[#7f7f7f] text-md w-10  text-center text-gray-950 font-bold leading-[21px]">Pilna suma:</span><span
                                class="relative justify-start text-[#191919] text-md font-semibold  leading-[16.80px]"> {{ $order->order_price  + 0.30 }} € &times; {{ $order->amount }} = {{ ($order->order_price + 0.30) * $order->amount }} €</span>
                        </p>
                        <p>
                                    <span
                                        class="relative justify-start text-[#7f7f7f] text-md w-10  text-center
                                        text-gray-950 font-bold leading-[21px]">{{ $order->takeaway ? "Išsinešimui!" : 'Vietoje!' }}</span>

                        </p>
                    @else
                        <p>
                                    <span
                                        class="relative justify-start text-[#7f7f7f] text-md w-10  text-center text-gray-950 font-bold leading-[21px]">Pilna suma:</span><span
                                class="relative justify-start text-[#191919] text-md font-semibold  leading-[16.80px]"> {{ $price}} € &times; {{ $order->amount }} = {{ $price * $order->amount }} €</span>
                        </p>
                    @endif
                </div>
            </div>

            <flux:modal.trigger  wire:click="openCommentForOrder({{ $order->id }})" name="add-comment-for-order-{{$order->id}}">
                <button class="flex items-center justify-center w-10 h-10 rounded-full text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
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



            <div class="flex items-center justify-center">
                <button wire:click="removeOneOrder({{$order->id}})" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-700 hover:bg-gray-500 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>

                <span  class="text-md w-4  text-center text-gray-950 font-bold mx-4">{{ $order->amount }}</span>

                <button wire:click="addOneOrder({{$order->id}})" class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-600 hover:bg-emerald-700 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12M6 12h12"></path>
                    </svg>
                </button>
            </div>
        </div>

    @endforeach
    <hr class="my-1 h-px border-t-0 bg-transparent bg-gradient-to-r from-transparent via-green-500 " />

    {{--su obuoliu paveiksleliu--}}
    {{--        <div class="flex  gap-2 mb-6 items-center"><img width="120" height="100" src="https://iili.io/3FqLBsI.png" alt="">
                <div>
                    <h3 class="w-[216px]   text-[#191919] text-sm font-normal  leading-[21px]">Fresh Indian Orange</h3>
                    <p>
                        <span
                            class="relative justify-start text-[#7f7f7f] text-sm font-normal  leading-[21px]">1 kg x</span><span
                            class="relative justify-start text-[#191919] text-sm font-semibold  leading-[16.80px]"> 12.00</span>
                    </p>
                </div>
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_629_6652)">
                        <path
                            d="M12 23C18.0748 23 23 18.0748 23 12C23 5.92525 18.0748 1 12 1C5.92525 1 1 5.92525 1 12C1 18.0748 5.92525 23 12 23Z"
                            stroke="#CCCCCC" stroke-miterlimit="10"></path>
                        <path d="M16 8L8 16" stroke="#666666" stroke-width="1.5" stroke-linecap="round"
                              stroke-linejoin="round">
                        </path>
                        <path d="M16 16L8 8" stroke="#666666" stroke-width="1.5" stroke-linecap="round"
                              stroke-linejoin="round">
                        </path>
                    </g>
                    <defs>
                        <clipPath id="clip0_629_6652">
                            <rect width="24" height="24" fill="white"></rect>
                        </clipPath>
                    </defs>
                </svg>
            </div>--}}

    <livewire:orders.category-sum/>
    <div class="">
        <div class=" py-2 flex justify-between items-center">
            <div class="font-semibold text-3xl  text-[#191919]">Iš viso</div>
            <div class="font-semibold text-3xl text-[#191919]"><livewire:orders.total-sum-manager/></div>
        </div>

        <button wire:click="makeOrder"
                class="w-full px-10 py-4 bg-emerald-700 rounded-2xl text-white/80 text-base font-semibold  leading-tight">
            Daryti užsakymą
        </button>

        <button wire:click="resetOrders"
                class="w-full mt-3 px-10 py-4 bg-emerald-600/10 rounded-2xl   text-emerald-700 text-base font-semibold  leading-tight">
            Valyti krepšelį
        </button>

        <flux:modal.trigger name="see-orders" class="bg-yellow-300">
            <flux:button class="w-full mt-3 px-10 py-8 bg-emerald-600/10 !rounded-2xl">Užsakymai</flux:button>
        </flux:modal.trigger>

        <flux:modal name="see-orders" variant="flyout" class="bg-yellow-300" >
            <livewire:kasa-orders/>
        </flux:modal>
    </div>
</div>
