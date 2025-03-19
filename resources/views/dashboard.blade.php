<x-layouts.app :title="__('Dashboard')">

    <div class="flex h-full w-full flex-1  gap-4 rounded-xl">
        <div class="auto-rows-min gap-4 w-3/4" data-tabs-toggle="#default-tab-content" role="tablist">
            <livewire:category-menu/>
            <livewire:product-menu/>
        </div>

        <div class="auto-rows-min gap-4 w-1/3 ">
            <livewire:orders-list/>
        </div>
    </div>
</x-layouts.app>
