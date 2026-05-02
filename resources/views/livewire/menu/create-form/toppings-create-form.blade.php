<div class="bg-white/80 rounded-xl px-5 py-4 backdrop-blur-[2px]">
    <h2 class="text-[#191919] text-base font-semibold mb-3">Sukurti produktą</h2>
    <div class="flex gap-3 items-end flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <label class="block mb-1 text-sm font-medium text-gray-600">Pavadinimas</label>
            <input wire:model="name" class="w-full px-3 py-2 rounded-lg bg-white border border-gray-200 shadow-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Įveskite pavadinimą..." />
            @error('name') <span class="text-sm text-red-500">Pavadinimas turi būti užpildytas</span> @enderror
        </div>
        <div class="w-36">
            <label class="block mb-1 text-sm font-medium text-gray-600">Kaina</label>
            <input wire:model="price" type="number" min="0" step="0.01" class="w-full px-3 py-2 rounded-lg bg-white border border-gray-200 shadow-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="0.00" />
            @error('price') <span class="text-sm text-red-500">Gali būti tik skaičiai</span> @enderror
        </div>
        <button wire:click="save" class="px-6 py-2 bg-emerald-700 hover:bg-emerald-600 rounded-lg text-white font-semibold whitespace-nowrap transition-colors">
            Sukurti produktą
        </button>
    </div>
</div>
