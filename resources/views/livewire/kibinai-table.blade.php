<div class="relative gap-3 mt-10 overflow-x-auto shadow-md sm:rounded-lg text-white/80">
    <div class="flex h-full w-full flex-1  gap-4 rounded-xl">
        <div class="auto-rows-min gap-4 w-3/4" data-tabs-toggle="#default-tab-content" role="tablist">
    <table class="w-full text-sm text-left  dark:text-gray-400 table-fixed">
        <thead class="text-xs uppercase bg-emerald-700/70">
        <tr>
            <th scope="col" class="px-6 py-3">
                Pavadinimas
            </th>
            <th scope="col" class="px-6 py-3">
                Rodyti
            </th>
            <th scope="col" class="px-6 py-3">
                Kaina
            </th>
            <th scope="col" class="px-6 py-3">
                Veiksmai
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($simpleProducts as $productName)
            <tr class="border-b border-green-500/30 bg-white/10">
                <th scope="row" class="@if($editedProductNameId === $productName->id) hidden @endif px-6 py-4 font-medium   dark:text-white">
                    {{ $productName->name }}
                </th>
                <td class="@if($editedProductNameId !== $productName->id) hidden @endif px-4 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                    <input type="text" wire:model.live.debounce="name" id="name" class="bg-white/80 py-4 pr-4 pl-2 rounded-lg  sm:text-base " >
                    @error('name')
                    <span class="text-sm text-red-500">hy7hy7h</span>
                    @enderror
                </td>
                <td class="px-6 py-4">
                </td>
                <th scope="row" class="@if($editedProductNameId === $productName->id) hidden @endif px-6 py-4 font-medium   dark:text-white">
                    {{ $productName->price }}
                </th>
                <td class="@if($editedProductNameId !== $productName->id) hidden @endif px-4 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                    <input type="text" wire:model.live.debounce="price" id="price" class="bg-white/80 py-4 pr-4 pl-2 rounded-lg  sm:text-base " >
{{--                    <div class="flex items-center">
                        <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div> Online


                        <div class="flex items-center gap-x-3">
                            <label for="hs-valid-toggle-switch" class="relative inline-block w-11 h-6 cursor-pointer">
                                <input type="checkbox" id="hs-valid-toggle-switch" wire:model="show" class="peer sr-only" checked="">
                                <span class="absolute inset-0 bg-red-200 rounded-full transition-colors duration-200 ease-in-out peer-checked:bg-gray-900/20 dark:bg-neutral-700 dark:peer-checked:bg-teal-500 peer-disabled:opacity-50 peer-disabled:pointer-events-none"></span>
                                <span class="absolute top-1/2 start-0.5 -translate-y-1/2 size-5 bg-white rounded-full shadow-xs transition-transform duration-200 ease-in-out peer-checked:translate-x-full dark:bg-neutral-400 dark:peer-checked:bg-white"></span>
                            </label>
                            <label for="hs-valid-toggle-switch" class="text-sm text-gray-500 dark:text-neutral-400">Valid switch</label>
                        </div>
                    </div>--}}
                    @error('price')
                    <span class="text-sm text-red-500">hy7hy7h</span>
                    @enderror
                </td>
                <td class="px-6 py-4">
                    @if($editedProductNameId === $productName->id)
                        <flux:button wire:click="save({{ $productName->id }})" variant="primary" class="bg-gray-900/20">Išsaugoti</flux:button>
                        <flux:button wire:click.prevent="cancel" variant="primary">Atšaukti</flux:button>
                    @else
                        <flux:button wire:click="editProduct({{ $productName->id }})" variant="primary">Pakeisti</flux:button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
        </div>
        <livewire:kibinai-create-form/>
    </div>
</div>
