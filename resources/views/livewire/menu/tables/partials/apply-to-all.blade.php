{{-- Set one price column for every product of this category at once. --}}
<div data-apply-to-all class="my-4 flex flex-wrap items-end gap-3 rounded-xl bg-white/80 px-5 py-4 backdrop-blur-[2px]">
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-600">Keisti visiems šios kategorijos produktams</label>
        <select wire:model="bulkField"
                class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            @foreach($this->bulkFieldOptions as $field => $label)
                <option value="{{ $field }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-36">
        <label class="mb-1 block text-sm font-medium text-gray-600">Nauja suma, €</label>
        <input wire:model="bulkValue" wire:keydown.enter="applyToAll" type="text" inputmode="decimal" placeholder="0,00"
               class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" />
    </div>
    <button type="button" wire:click="applyToAll"
            wire:confirm="Pakeisti šią sumą visiems šios kategorijos produktams?"
            class="whitespace-nowrap rounded-lg bg-emerald-700 px-6 py-2 font-semibold text-white transition-colors hover:bg-emerald-600">
        Taikyti visiems
    </button>
    @if(method_exists($this, 'setKitchenForAll'))
        <div data-kitchen-for-all class="flex items-end gap-3 sm:ms-6 sm:border-s sm:border-gray-200 sm:ps-6">
            <div>
                <span class="mb-1 block text-sm font-medium text-gray-600">Spausdinti virtuvei</span>
                <div class="flex gap-2">
                    <button type="button" wire:click="setKitchenForAll(true)"
                            wire:confirm="Spausdinti virtuvei visus šios kategorijos produktus?"
                            class="whitespace-nowrap rounded-lg bg-emerald-700 px-4 py-2 font-semibold text-white transition-colors hover:bg-emerald-600">
                        Taip visiems
                    </button>
                    <button type="button" wire:click="setKitchenForAll(false)"
                            wire:confirm="Nespausdinti virtuvei nė vieno šios kategorijos produkto?"
                            class="whitespace-nowrap rounded-lg bg-gray-600 px-4 py-2 font-semibold text-white transition-colors hover:bg-gray-500">
                        Ne visiems
                    </button>
                </div>
            </div>
        </div>
    @endif
    @error('bulkValue') <span class="w-full text-sm text-red-500">{{ $message }}</span> @enderror
</div>
