<div x-data x-init="initSwapSortable($el, $wire, 'updateOrder')" class="grid md:grid-cols-3 auto-rows-min gap-4 mt-10 mt-4">
    @foreach(\App\Models\Drink::where('show', true)->orderBy('position')->get() as $productName)
        <div data-sortable-id="{{ $productName->id }}" wire:key="drink-{{ $productName->id }}" wire:click="addProduct('{{ $productName->name }}', '{{ $productName->price }}')"
            class="relative text-center aspect-video overflow-hidden rounded-2xl cursor-pointer
                bg-white/10 backdrop-blur-lg border border-white/25
                hover:bg-white/20 hover:scale-[1.03] hover:-translate-y-0.5
                active:scale-[0.98]
                transition-all duration-300"
            style="box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.25);">
            <div class="flex grid content-center flex-col gap-2 h-full text-white rounded-2xl w-full">
                <div class="font-extrabold text-2xl tracking-wide antialiased" style="text-shadow: 0 0 20px rgba(255,255,255,0.15), 0 2px 4px rgba(0,0,0,0.3);">{{ $productName->name }}</div>
            </div>
        </div>
    @endforeach
</div>
