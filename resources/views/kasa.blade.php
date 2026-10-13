<x-layouts.app :title="__('Dashboard')">
    @php $panelStyle = 'background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.2); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.2);'; @endphp
    <div class="flex w-full flex-1 items-start gap-4">
        <aside class="sticky top-6 flex h-[calc(100dvh-3rem)] w-48 shrink-0 flex-col overflow-hidden rounded-3xl lg:top-8 lg:h-[calc(100dvh-4rem)] xl:w-56"
               style="{{ $panelStyle }}">
            <div class="flex items-center gap-3 border-b border-white/10 px-4 py-4 font-nunito">
                <button onclick="window.location='{{ route('home') }}'" title="Atgal"
                        class="flex size-11 shrink-0 touch-manipulation items-center justify-center rounded-xl bg-white/10 text-white/80 ring-1 ring-white/15 transition-all duration-100 hover:bg-white/20 active:scale-[0.95]">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7M3 12h18"></path>
                    </svg>
                </button>
                <h2 class="text-xl font-extrabold tracking-wide text-white antialiased">Meniu</h2>
            </div>
            <div class="min-h-0 flex-1 p-3">
                <livewire:kasa-menu/>
            </div>
        </aside>

        <section class="sticky top-6 h-[calc(100dvh-3rem)] min-w-0 flex-1 overflow-hidden rounded-3xl lg:top-8 lg:h-[calc(100dvh-4rem)]"
                 style="{{ $panelStyle }}">
            <livewire:kasa-category-section/>
        </section>

        <div class="sticky top-6 h-[calc(100dvh-3rem)] w-[28%] min-w-80 max-w-md shrink-0 lg:top-8 lg:h-[calc(100dvh-4rem)]">
            <livewire:orders.orders-list/>
        </div>
    </div>
    <livewire:ready-orders/>
</x-layouts.app>
