<x-layouts.app :title="__('Dashboard')">
    <div class="flex w-full flex-1 items-start gap-5">
        <div class="flex min-w-0 flex-1 flex-col gap-1 pb-24">
            <div class="flex items-start gap-2.5">
                <button onclick="window.location='{{ route('home') }}'"
                        class="flex size-16 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-white ring-1 ring-white/15 transition-all duration-200 hover:bg-white/20 active:scale-[0.97]">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7M3 12h18"></path>
                    </svg>
                </button>
                <div class="@container min-w-0 flex-1">
                    <livewire:kasa-menu/>
                </div>
            </div>
            <livewire:kasa-category-section/>
        </div>

        <div class="sticky top-6 h-[calc(100dvh-3rem)] w-[30%] min-w-80 max-w-md shrink-0 lg:top-8 lg:h-[calc(100dvh-4rem)]">
            <livewire:orders.orders-list/>
        </div>
    </div>
    <livewire:ready-orders/>
</x-layouts.app>
