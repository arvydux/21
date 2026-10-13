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
    @error('bulkValue') <span class="w-full text-sm text-red-500">{{ $message }}</span> @enderror
</div>
