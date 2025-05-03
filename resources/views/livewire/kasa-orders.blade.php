<div wire:poll.5s class="">
    <div class="grid md:grid-cols-2 auto-rows-min gap-2">
        @foreach(\App\Models\OrderNumbers::where('is_ready', true)->where('is_taken', false)->orderBy('updated_at', 'desc')->get() as $order)
            <div wire:click="makeOrderTaken('{{ $order->number }}')"  class="relative text-center shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
                aspect-video overflow-hidden rounded-xl  bg-emerald-600/80  dark:border-neutral-700">
                <div class="flex grid content-center flex-col gap-2 h-full text-white/80 rounded-xl w-full">
                    <div class="font-semibold text-8xl">{{ $order->number }}</div>
                </div>
            </div>
        @endforeach
    </div>
    <div wire:click="toogleTakenOrders()" class="relative my-4 text-center shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
                 rounded-xl  bg-red-600/70  dark:border-neutral-700">
        <div class="flex grid content-center flex-col gap-2 h-24 text-white/80 rounded-xl w-full">
            <div class="font-semibold text-4xl">Grąžinti</div>
        </div>
    </div>

    @if($showTakenOrders)
        <div class="grid md:grid-cols-2 auto-rows-min gap-2">
            @foreach(\App\Models\OrderNumbers::where('is_ready', true)->where('is_taken', true)->orderBy('updated_at', 'desc')->get() as $order)
                <div wire:click="makeOrderNotTaken('{{ $order->number }}')" class="relative text-center shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
                        aspect-video overflow-hidden rounded-xl   bg-red-500/90  dark:border-neutral-700">
                    <div class="flex grid content-center flex-col gap-2 h-full text-white/80 rounded-xl w-full">
                        <div class="font-semibold text-8xl">{{ $order->number }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
