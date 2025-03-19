<div class="grid auto-rows-min gap-4 md:grid-cols-3 " id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
    <button type="button" wire:click="setView('test')" >vytvytvty
    </button>

    <button type="button" wire:click="setView('test2')" >vytvytvty
    </button>

    @if(isset($viewName))
        <livewire:dynamic-component :component="'orders-list'" :key="$viewName" :mydata="67567567"/>
    @endif
    @foreach(\App\Models\SimpleProduct::all() as $productName)
        <div wire:click="addProduct('{{ $productName->name }}', '{{ $productName->price }}')"  class="relative text-center  shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
            aspect-video overflow-hidden rounded-xl  dark:border-neutral-700">
            <flux:modal.trigger name='open-modal'>
                <div
                    class="flex grid content-center flex-col gap-2 h-full text-white rounded-xl
                     w-full
                       bg-white/20 ">
                    <div class="font-semibold text-lg">GOLD</div>
                    <div class="font-semibold text-5xl tracking-tight">Jautiena</div>

                </div>
            </flux:modal.trigger>


        </div>
    @endforeach

    <flux:modal name='open-modal' >



        <div class="grid auto-rows-min gap-4 w-full h-full ">
            <div
                class="flex flex-col gap-2  text-white rounded-xl
                 shadow-md p-6
                   bg-white/20">
                <div class="font-semibold text-lg">Today</div>
                <div class="font-semibold text-5xl tracking-tight">$12.921</div>
                <div class="font-normal">Gross volume</div>
            </div>
            <div
                class="flex flex-col gap-2  text-white rounded-xl shadow-md p-6  bg-gray-200 bg-opacity-30 backdrop-filter backdrop-blur-lg">
                <div class="font-semibold text-lg">Today</div>
                <div class="font-semibold text-5xl tracking-tight">$12.921</div>
                <div class="font-normal">Gross volume</div>
            </div>
            <div
                class="flex flex-col gap-2  text-white rounded-xl shadow-md p-6
                 bg-emerald-800 ">
                <div class="font-semibold text-lg">Today</div>

                <div class="font-normal">Gross volume</div>
            </div>
        </div>

    </flux:modal>
</div>


<div class="grid auto-rows-min gap-4 md:grid-cols-3 " id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">            <div class="grid md:grid-cols-5 auto-rows-min gap-4">
        @foreach(\App\Models\SimpleProduct::all() as $productName)
            <div wire:click=""  class="relative text-center  shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
            aspect-video overflow-hidden rounded-xl  dark:border-neutral-700">
                <div class="flex grid content-center flex-col gap-2 h-full text-white rounded-xl w-full bg-white/20 ">
                    <div class="font-semibold text-lg">GOLD</div>
                    <div class="font-semibold text-5xl tracking-tight">Jautiena</div>
                </div>
            </div>
        @endforeach
    </div>
    @foreach(\App\Models\SimpleProduct::all() as $productName)
        <div wire:click="addProduct('{{ $productName->name }}', '{{ $productName->price }}')"  class="relative text-center  shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
            aspect-video overflow-hidden rounded-xl  dark:border-neutral-700">
            <flux:modal.trigger name='open-modal'>
                <div
                    class="flex grid content-center flex-col gap-2 h-full text-white rounded-xl
                     w-full
                       bg-white/20 ">
                    <div class="font-semibold text-lg">GOLD</div>
                    <div class="font-semibold text-5xl tracking-tight">Jautiena</div>

                </div>
            </flux:modal.trigger>


        </div>
    @endforeach
</div>

