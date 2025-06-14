@php use Illuminate\Support\Facades\Cache; @endphp
<div class="fixed bottom-0 left-0 w-full text-center p-2">
    <div wire:poll.5s  class="grid md:grid-cols-10 auto-rows-min gap-1" >
        @foreach(\App\Models\OrderNumbers::where('is_ready', true)->where('is_taken', false)->orderBy('updated_at', 'desc')->get() as $order)
            <div onclick="playAlert()" wire:click="makeOrderTaken('{{ $order->number }}')"  class="text-center shadow-md p-3 hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
                    aspect-video overflow-hidden rounded-xl  bg-emerald-800/80  dark:border-neutral-700">
                <div class="flex grid content-center flex-col gap-2 h-full text-white/80 rounded-xl w-full">
                    <div class="font-semibold text-6xl">{{ $order->number }}</div>
                </div>
            </div>
        @endforeach

{{--            {{ $firstTime }}
            @if(Cache::get('playSound') || isset($firstTime))
                <div  wire:click="notFirstTime">

                    <button  id="myButton" onclick="playAlert()">Ijungti garsa</button>
                </div>
                @php Cache::put('playSound', 0, 60 * 60 * 24) @endphp
            @endif--}}
            @if(Cache::get('playSound') || (($firstTime === true)))
                <div>
                    @if($firstTime)
                        <button id="myButton" wire:click="setFirstTime(false)" onclick="playAlert()">
                            Aktyvuoti garsa
                        </button>
                    @else
                    <button id="myButton" wire:doubleclick="notNotFirstTime()" onclick="playAlert()">

                    </button>
                    @endif
                </div>
                @php Cache::put('playSound', 0, 60 * 60 * 24) @endphp
            @endif
    </div>
</div>
<script>

    function playAlert() {
        const audio = new Audio('/14.mp3');
        audio.play();
    }

    document.addEventListener('DOMContentLoaded', () => {
        setInterval(() => {
            document.getElementById('myButton').click();
        }, 5000); // 5000ms = 5 seconds
    });
</script>

