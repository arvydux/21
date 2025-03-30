<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1  gap-4 rounded-xl">
        <div class="auto-rows-min gap-4 w-6/10" data-tabs-toggle="#default-tab-content" role="tablist">
            <livewire:kasa-menu/>
            <livewire:kasa-category-section/>
        </div>

        <div class="auto-rows-min gap-4 w-4/10 ">
            <livewire:orders.orders-list/>
        </div>
    </div>
</x-layouts.app>
