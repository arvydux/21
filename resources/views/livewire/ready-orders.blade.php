<div class="fixed bottom-0 left-0 w-full text-center p-2">
    <div wire:poll.5s  class="grid md:grid-cols-10 auto-rows-min gap-1" >
        @foreach(\App\Models\OrderNumbers::where('is_ready', false)->get() as $order)
            <div onclick="setTimeout(playAlert, 5000)" wire:click="makeOrderTaken('{{ $order->number }}')"  class="text-center shadow-md p-3 hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
                    aspect-video overflow-hidden rounded-xl  bg-yellow-400/90 ">
                <div class="flex grid content-center flex-col gap-2 h-full text-white/80 rounded-xl w-full">
                    <div class="font-semibold text-6xl">{{ $order->number }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<script>
    function playAlert() {
        const audio = new Audio('/14.mp3');
        audio.play();
    }

</script>


