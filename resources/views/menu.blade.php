<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1  gap-4 rounded-xl">
        <div class="auto-rows-min gap-4 w-4/4" data-tabs-toggle="#default-tab-content" role="tablist">
            <livewire:category-menu/>
            <livewire:product-tables/>
        </div>
    </div>
</x-layouts.app>
