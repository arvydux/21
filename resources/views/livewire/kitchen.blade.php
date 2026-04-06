<div wire:poll.5s class="grid md:grid-cols-2 auto-rows-min gap-4">
    <div class="grid md:grid-cols-2 auto-rows-min gap-4 mr-8">
        <div class="col-span-2">
            <div class="flex grid content-center text-white/80 rounded-xl ">
                <div class="font-semibold text-4xl">Gaminami:</div>
            </div>
        </div>
        @foreach(\App\Models\OrderNumbers::where('is_ready', false)->get() as $order)
        <div wire:click="makeOrderReady('{{ $order->number }}')"  class="relative text-center shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
                aspect-video overflow-hidden rounded-xl {{ $order->by_phone ? 'bg-white' : 'bg-yellow-400/90' }}
                {{ $order->by_phone ? 'zoom-in-out-box' : 'text-white' }}" >
            <div class="flex grid content-center flex-col gap-2 h-full rounded-xl w-full">
                <div class=" font-semibold text-6xl">{{ $order->number }}</div>
                <div class=" font-semibold text-2xl">{{ (int)$order->minutes_since_created }} min.</div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="grid md:grid-cols-2 auto-rows-min gap-4  ml-8">
        <div class="col-span-2">
            <div class="flex grid content-center text-white/80 rounded-xl ">
                <div class="font-semibold text-4xl">Paruošti:</div>
            </div>
        </div>
        @foreach(\App\Models\OrderNumbers::where('is_ready', true)->where('is_taken', false)->orderBy('updated_at', 'desc')->get() as $order)
            <div wire:click="makeOrderNotReady('{{ $order->number }}')" class="relative text-center shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
                aspect-video overflow-hidden rounded-xl  bg-emerald-600  dark:border-neutral-700">
                <div class="flex grid content-center flex-col gap-2 h-full text-white/80 rounded-xl w-full">
                    <div class="font-semibold text-8xl">{{ $order->number }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>
