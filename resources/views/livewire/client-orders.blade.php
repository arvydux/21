<div wire:poll.5s class="w-screen h-screen relative overflow-hidden flex" style="background-color: #030712;">

    {{-- Background glows --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute inset-0" style="background: radial-gradient(ellipse 55% 80% at 15% 50%, rgba(245,158,11,0.1) 0%, transparent 70%)"></div>
        <div class="absolute inset-0" style="background: radial-gradient(ellipse 55% 80% at 85% 50%, rgba(16,185,129,0.1) 0%, transparent 70%)"></div>
        <div class="absolute inset-0" style="background: radial-gradient(ellipse 30% 40% at 50% 100%, rgba(255,255,255,0.03) 0%, transparent 60%)"></div>
    </div>

    {{-- Vertical divider --}}
    <div class="absolute top-0 bottom-0 left-1/2 -translate-x-1/2 w-px pointer-events-none"
         style="background: linear-gradient(to bottom, transparent 0%, rgba(255,255,255,0.06) 15%, rgba(255,255,255,0.06) 85%, transparent 100%)"></div>

    <style>
        .card-ready { box-shadow: 0 4px 24px rgba(5,150,105,0.4), inset 0 1px 0 rgba(255,255,255,0.2), inset 0 -2px 0 rgba(0,0,0,0.2); }
    </style>

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

    {{-- LEFT: Gaminami --}}
    <div class="flex-1 flex flex-col min-w-0 p-8 pr-6 gap-6">

        <div class="shrink-0">
            <div class="uppercase" style="font-family: 'Manrope', sans-serif; font-weight: 800; font-size: clamp(2rem, 4vw, 3.5rem); letter-spacing: 0.1em; color: rgba(245,158,11,0.5);">Gaminami</div>
        </div>

        <div class="flex-1 grid gap-3 content-start" style="grid-template-columns: repeat(3, 1fr);">
            @foreach($gaminamiOrders as $order)
                <div class="relative text-center aspect-video overflow-hidden rounded-2xl"
                     style="background: linear-gradient(145deg, #f59e0b 0%, #d97706 100%);
                            box-shadow: 0 4px 24px rgba(245,158,11,0.3), inset 0 1px 0 rgba(255,255,255,0.25), inset 0 -2px 0 rgba(0,0,0,0.2);">
                    <div class="absolute inset-0" style="background: radial-gradient(ellipse 80% 60% at 50% 10%, rgba(255,255,255,0.18) 0%, transparent 70%)"></div>
                    <div class="relative flex items-center justify-center h-full">
                        <span class="text-white" style="font-family: 'DM Sans', sans-serif; font-weight: 900; font-size: clamp(1.8rem, 4.5vw, 3.8rem); font-variant-numeric: tabular-nums; text-shadow: 0 2px 8px rgba(0,0,0,0.25);">{{ $order->number }}</span>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    {{-- RIGHT: Paruošti --}}
    <div class="flex-1 flex flex-col min-w-0 p-8 pl-6 gap-6">

        <div class="shrink-0">
            <div class="uppercase" style="font-family: 'Manrope', sans-serif; font-weight: 800; font-size: clamp(2rem, 4vw, 3.5rem); letter-spacing: 0.1em; color: rgba(16,185,129,0.5);">Paruošti</div>
        </div>

        <div class="flex-1 grid gap-3 content-start" style="grid-template-columns: repeat(3, 1fr);">
            @foreach($paruostiOrders as $order)
                <div class="relative text-center aspect-video overflow-hidden rounded-2xl card-ready"
                     style="background: linear-gradient(145deg, #10b981 0%, #059669 100%);
                            box-shadow: 0 4px 24px rgba(5,150,105,0.35), inset 0 1px 0 rgba(255,255,255,0.2), inset 0 -2px 0 rgba(0,0,0,0.2);
                            border: 1px solid rgba(52,211,153,0.2);">
                    <div class="absolute inset-0" style="background: radial-gradient(ellipse 80% 60% at 50% 10%, rgba(255,255,255,0.15) 0%, transparent 70%)"></div>
                    <div class="relative flex items-center justify-center h-full">
                        <span class="text-white" style="font-family: 'DM Sans', sans-serif; font-weight: 900; font-size: clamp(1.8rem, 4.5vw, 3.8rem); font-variant-numeric: tabular-nums; text-shadow: 0 2px 8px rgba(0,0,0,0.3);">{{ $order->number }}</span>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    @endif
</div>
