<div class="h-full min-h-screen w-full bg-gray-800 pt-12 p-4 ">
    <div wire:poll.5s="fetchOrders" class="grid gap-14 md:grid-cols-3 md:gap-5">

        @foreach($orders as $order)
        @foreach(json_decode($order->name) as $name)
            @foreach($name as $name => $price)
                <div wire:click="removeOrder({{$order->id}})"  class="flex flex-col rounded-lg bg-white shadow-sm max-w-96 p-8 my-6 border border-slate-200">
                    <div class="pb-8 m-0 mb-8 text-center text-slate-800 border-b border-slate-200">
                        <p class="text-lg-center uppercase font-semibold text-slate-500">
                            {{ $name }}
                        </p>
                        Vietoje
                        <h1 class="flex justify-center gap-1 mt-4 font-bold text-slate-800 text-6xl">
                            <span class="text-3xl"></span>A{{ $order->id }}
                            <br><br>
                            <span class="self-end text-3xl">{{ \Carbon\Carbon::parse($order->created_at)->addHours(2)->format('H:i:s') }}</span>
                        </h1>
                        Vietoje
                    </div>
                    <div class="p-0">
                        <ul class="flex flex-col gap-4">
                            @foreach(json_decode($order->toppings) as $topping)
                                @foreach($topping as $name => $price)





                            <li class="flex items-center gap-4">
        <span class="p-1 border rounded-full border-slate-200 bg-slate-50">
          <svg xmlns="http://www.w3.org/2000/svg"
               fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-600">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
          </svg>
        </span>
                                <p class="text-slate-500">
                                    {{ $name }}
                                </p>
                            </li>

                                @endforeach
                            @endforeach
                        </ul>
                    </div>
                    <div class="p-0 mt-12">
                        <button class="min-w-32 w-full rounded-md bg-slate-800 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none" type="button">
                            Padaryta
                        </button>
                    </div>
                </div>
            @endforeach
        @endforeach


        @endforeach










    </div>
</div>


