<div class="fixed bottom-0 left-0 w-full text-center p-2">
    <div wire:poll.5s  class="grid md:grid-cols-10 auto-rows-min gap-1" >
            @foreach(\App\Models\OrderNumbers::where('is_ready', true)->where('is_taken', false)->orderBy('updated_at', 'desc')->get() as $order)
            <div wire:click="makeOrderTaken('{{ $order->number }}')"  class="text-center shadow-md p-3 hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
                    aspect-video overflow-hidden rounded-xl  bg-emerald-800/80  dark:border-neutral-700">
                <div class="flex grid content-center flex-col gap-2 h-full text-white/80 rounded-xl w-full">
                    <div class="font-semibold text-6xl">{{ $order->number }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>

