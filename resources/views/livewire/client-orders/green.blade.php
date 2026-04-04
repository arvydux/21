<div class="w-screen h-screen flex" style="background: radial-gradient(circle at 50% 50%, rgba(226,232,240,0.2) 0%, rgba(226,232,240,0.1) 25%, rgba(226,232,240,0.05) 35%, transparent 50%), #000;">

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
    <div class="flex-1 flex flex-col min-w-0 p-10 pr-6 gap-5">
        <div style="font-family: 'Manrope', sans-serif; font-weight: 800; font-size: clamp(2.2rem, 4.5vw, 3.8rem); color: #e2e8f0; letter-spacing: 0.04em; line-height: 1;">GAMINAMI</div>
        <div class="grid gap-3 content-start" style="grid-template-columns: repeat(3, 1fr);">
            @foreach($gaminamiOrders as $order)
                <div class="relative flex items-center justify-center rounded-2xl"
                     style="aspect-ratio: 4/3; background: #f5a520;
                            box-shadow: 0 3px 12px rgba(0,0,0,0.13), 0 1px 3px rgba(0,0,0,0.08);">
                    <span style="font-family: 'DM Sans', sans-serif; font-weight: 900; font-size: clamp(2.2rem, 5.5vw, 4.5rem); font-variant-numeric: tabular-nums; color: #fff; text-shadow: 0 1px 4px rgba(0,0,0,0.15);">{{ $order->number }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- RIGHT: Paruošti --}}
    <div class="flex-1 flex flex-col min-w-0 p-10 pl-6 gap-5">
        <div style="font-family: 'Manrope', sans-serif; font-weight: 800; font-size: clamp(2.2rem, 4.5vw, 3.8rem); color: #e2e8f0; letter-spacing: 0.04em; line-height: 1;">PARUOŠTI</div>
        <div class="grid gap-3 content-start" style="grid-template-columns: repeat(3, 1fr);">
            @foreach($paruostiOrders as $order)
                <div class="relative flex items-center justify-center rounded-2xl"
                     style="aspect-ratio: 4/3; background: #48a855;
                            box-shadow: 0 3px 12px rgba(0,0,0,0.13), 0 1px 3px rgba(0,0,0,0.08);">
                    <svg class="absolute top-2.5 right-2.5" style="width:1.1rem; height:1.1rem; opacity:0.55;" viewBox="0 0 20 20" fill="white">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" clip-rule="evenodd"/>
                    </svg>
                    <span style="font-family: 'DM Sans', sans-serif; font-weight: 900; font-size: clamp(2.2rem, 5.5vw, 4.5rem); font-variant-numeric: tabular-nums; color: #fff; text-shadow: 0 1px 4px rgba(0,0,0,0.15);">{{ $order->number }}</span>
                </div>
            @endforeach
        </div>
    </div>

    @endif
</div>
