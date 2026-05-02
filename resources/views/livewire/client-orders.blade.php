@script
<script>
    $wire.on('show-order-ready', ({ numbers }) => {
        var overlayQueue = numbers.slice();
        var overlayShowing = false;

        function showNextOverlay() {
            if (overlayQueue.length === 0) { overlayShowing = false; return; }
            overlayShowing = true;
            var number = overlayQueue.shift();

            var overlay = document.getElementById('order-ready-overlay');
            var card    = document.getElementById('order-ready-card');
            var numEl   = document.getElementById('order-ready-number');
            var check   = document.getElementById('order-ready-check');

            numEl.textContent = number;
            overlay.style.display = 'flex';

            card.classList.remove('ordr-in', 'ordr-out');
            void card.offsetWidth;
            card.classList.add('ordr-in');

            check.style.animation = 'none';
            void check.offsetWidth;
            check.style.animation = 'ordr-check-draw 0.4s 0.3s ease forwards';

            setTimeout(function () {
                card.classList.remove('ordr-in');
                card.classList.add('ordr-out');
                setTimeout(function () {
                    overlay.style.display = 'none';
                    var orderCard = document.querySelector('[data-order-number="' + number + '"]');
                    if (orderCard) {
                        orderCard.classList.add('ordr-card-pulse');
                        orderCard.addEventListener('animationend', function () {
                            orderCard.classList.remove('ordr-card-pulse');
                        }, { once: true });
                    }
                    showNextOverlay();
                }, 550);
            }, 3200);
        }

        if (!overlayShowing) showNextOverlay();
    });
</script>
@endscript

<div wire:poll.5s="checkAndShowAnimation">
    <style>
        @keyframes ordr-scale-in {
            0%   { opacity: 0; transform: scale(0.6); }
            65%  { opacity: 1; transform: scale(1.04); }
            100% { opacity: 1; transform: scale(1); }
        }
        @keyframes ordr-scale-out {
            0%   { opacity: 1; transform: scale(1); }
            100% { opacity: 0; transform: scale(1.15); }
        }
        @keyframes ordr-pulse-ring {
            0%   { transform: scale(0.85); opacity: 0.7; }
            100% { transform: scale(1.45); opacity: 0; }
        }
        @keyframes ordr-check-draw {
            from { stroke-dashoffset: 80; }
            to   { stroke-dashoffset: 0; }
        }
        .ordr-in  { animation: ordr-scale-in  0.45s cubic-bezier(.22,.68,0,1.4) forwards; }
        .ordr-out { animation: ordr-scale-out 0.55s ease-in forwards; }
        @keyframes ordr-card-pulse {
            0%, 100% { transform: scale(1);    box-shadow: 0 0  0px rgba(255,255,255,0); }
            50%       { transform: scale(1.08); box-shadow: 0 0 30px rgba(255,255,255,0.7); }
        }
        .ordr-card-pulse { animation: ordr-card-pulse 0.65s ease-in-out 20; }
    </style>

    {{-- Order-ready full-screen overlay --}}
    <div id="order-ready-overlay" wire:ignore
         style="display:none; position:fixed; inset:0; z-index:9999;
                align-items:center; justify-content:center;
                background:rgba(0,0,0,0.72); backdrop-filter:blur(6px);">

        <div id="order-ready-card"
             style="position:relative; display:flex; flex-direction:column; align-items:center; gap:1.5rem;">

            <div style="position:absolute; inset:-40px; border-radius:50%;
                        background:radial-gradient(circle, rgba(34,197,94,0.25) 0%, transparent 70%);
                        animation:ordr-pulse-ring 1.2s ease-out infinite;"></div>
            <div style="position:absolute; inset:-40px; border-radius:50%;
                        background:radial-gradient(circle, rgba(34,197,94,0.15) 0%, transparent 70%);
                        animation:ordr-pulse-ring 1.2s ease-out 0.4s infinite;"></div>

            <div style="position:relative; display:flex; flex-direction:column; align-items:center;
                        gap:1rem; padding:3.5rem 5rem; border-radius:2.5rem;
                        background:linear-gradient(145deg,#16a34a,#15803d);
                        box-shadow:0 0 80px rgba(34,197,94,0.5), 0 24px 64px rgba(0,0,0,0.5);
                        border:2px solid rgba(255,255,255,0.15);">

                <svg viewBox="0 0 48 48" style="width:5rem; height:5rem;" fill="none">
                    <circle cx="24" cy="24" r="22" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
                    <polyline id="order-ready-check"
                              points="13,25 21,33 35,17" stroke="white" stroke-width="4"
                              stroke-linecap="round" stroke-linejoin="round"
                              stroke-dasharray="80" stroke-dashoffset="80"/>
                </svg>

                <div id="order-ready-number"
                     style="font-family:'DM Sans',sans-serif; font-weight:900;
                            font-size:clamp(6rem,18vw,14rem); line-height:1;
                            color:#fff; text-shadow:0 4px 24px rgba(0,0,0,0.3);
                            font-variant-numeric:tabular-nums;"></div>

                <div style="font-family:'Manrope',sans-serif; font-weight:800;
                            font-size:clamp(1.4rem,3vw,2.2rem); letter-spacing:0.12em;
                            color:rgba(255,255,255,0.85); text-transform:uppercase;">
                    Paruoštas!
                </div>
            </div>
        </div>
    </div>

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
</div>
