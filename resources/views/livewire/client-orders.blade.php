<div>
    {{-- Main content --}}
    <div>
        @if(\App\Models\OrderNumbers::count() === 0)
            <video id="temporaryDiv" style="display:none; z-index:9999;" autoplay muted class="absolute inset-0 object-cover w-full h-full">
                <source src="{{ asset('5.mp4') }}" type="video/mp4">
            </video>
            <script>
                const div = document.getElementById('temporaryDiv');
                div.style.display = 'block';
                setTimeout(() => { div.style.display = 'none'; }, 300000);
            </script>
        @else
            @php
                $gaminamiOrders = \App\Models\OrderNumbers::where('is_ready', false)->get();
                $paruostiOrders = \App\Models\OrderNumbers::where('is_ready', true)->where('is_taken', false)->orderBy('updated_at', 'desc')->get();
            @endphp
            @include('livewire.client-orders.' . cache('pos_template', 'green'))
        @endif
    </div>

    <script>
        (function () {
            if (window.__posOrderReadySetup) return;
            window.__posOrderReadySetup = true;

            var audioQueue = [];
            var audioPlaying = false;

            function playNextAudio() {
                if (audioQueue.length === 0) { audioPlaying = false; return; }
                audioPlaying = true;
                var src = audioQueue.shift();
                var audio = new Audio(src);
                audio.addEventListener('ended', playNextAudio);
                audio.addEventListener('error', playNextAudio);
                audio.play();
            }

            function onOrderReady(data) {
                var number = data && data.number !== undefined ? data.number : (Array.isArray(data) ? data[0] : null);
                audioQueue.push('/ona/paruostas.mp3');
                audioQueue.push('/ona/' + number + '.mp3');
                if (!audioPlaying) playNextAudio();
            }

            if (typeof Livewire !== 'undefined') {
                Livewire.on('play-order-ready', onOrderReady);
            } else {
                document.addEventListener('livewire:init', function () {
                    Livewire.on('play-order-ready', onOrderReady);
                });
            }
        })();
    </script>
</div>
