<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1  gap-4 rounded-xl">
        <div class="auto-rows-min gap-4 w-6/10" data-tabs-toggle="#default-tab-content" role="tablist">
            <livewire:kasa-menu/>
            <livewire:kasa-category-section/>
                <div class="relative left-0 bottom-0 my-2">
                    <button onclick="window.location='{{ route('home') }}'" class="flex items-center justify-center w-20 h-20 rounded-full text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7M3 12h18"></path>
                        </svg>
                    </button>
                </div>

        </div>

        <div class="auto-rows-min gap-4 w-4/10 ">
            <livewire:orders.orders-list/>
        </div>
    </div>
        <livewire:ready-orders/>
</x-layouts.app>
