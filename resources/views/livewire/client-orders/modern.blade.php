<div class="w-screen h-screen flex" style="background: #eef0f5;">

    {{-- LEFT: Gaminami --}}
    <div class="flex-1 flex flex-col min-w-0 p-10 pr-6 gap-6">

        <div style="font-family: 'Manrope', sans-serif; font-weight: 800; font-size: clamp(2.2rem, 4.5vw, 3.8rem); color: #0f172a; letter-spacing: 0.02em; line-height: 1;">GAMINAMI</div>

        {{-- Cards --}}
        <div class="grid gap-3 content-start" style="grid-template-columns: repeat(3, 1fr);">
            @foreach($gaminamiOrders as $order)
                <div class="relative flex items-center justify-center rounded-2xl overflow-hidden"
                     style="aspect-ratio: 4/3; background: #ffffff;
                            border-left: 5px solid #f5622d;
                            box-shadow: 0 2px 12px rgba(0,0,0,0.07), 0 1px 3px rgba(0,0,0,0.05);">
                    <span style="font-family: 'DM Sans', sans-serif; font-weight: 900; font-size: clamp(2.2rem, 5.5vw, 4.5rem); font-variant-numeric: tabular-nums; color: #0f172a;">{{ $order->number }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Vertical divider --}}
    <div class="self-stretch w-px my-8" style="background: #d1d5db;"></div>

    {{-- RIGHT: Paruošti --}}
    <div class="flex-1 flex flex-col min-w-0 p-10 pl-10 gap-6">

        <div style="font-family: 'Manrope', sans-serif; font-weight: 800; font-size: clamp(2.2rem, 4.5vw, 3.8rem); color: #0f172a; letter-spacing: 0.02em; line-height: 1;">PARUOŠTI</div>

        {{-- Cards --}}
        <div class="grid gap-5 content-start" style="grid-template-columns: repeat(3, 1fr);">
            @foreach($paruostiOrders as $order)
                <div class="relative flex items-center justify-center rounded-2xl"
                     style="aspect-ratio: 4/3; background: #3da85a;
                            box-shadow: 0 2px 12px rgba(0,0,0,0.10), 0 1px 3px rgba(0,0,0,0.06);">
                    <svg class="absolute top-2.5 right-2.5" style="width:1rem; height:1rem; opacity:0.55;" viewBox="0 0 20 20" fill="white">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" clip-rule="evenodd"/>
                    </svg>
                    <span style="font-family: 'DM Sans', sans-serif; font-weight: 900; font-size: clamp(2.2rem, 5.5vw, 4.5rem); font-variant-numeric: tabular-nums; color: #fff; text-shadow: 0 1px 4px rgba(0,0,0,0.15);">{{ $order->number }}</span>
                </div>
            @endforeach
        </div>
    </div>

</div>
