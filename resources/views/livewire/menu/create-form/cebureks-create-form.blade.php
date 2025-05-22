<div class="auto-rows-min gap-4 w-1/3 rounded-2xl">
    <div class="bg-white/80 flex flex-col  p-8 rounded-xl backdrop-blur-[2px] " >
        <div class="flex items-center mb-3 justify-between ">
            <h2 class="text-[#191919] text-xl font-medium leading-[30px]">Sukurti produktus</h2>
        </div>

        <div class="w-full min-w-[200px] my-2">
            <label class="block mb-2 font-medium font-normal text-gray-600">
                Pavadinimas
            </label>
            <input wire:model="name" class="w-full rounded-md  g-white/80 px-4 py-1.5  pl-4  rounded-lg bg-white/80 shadow-sm focus:shadow text-gray-600"  />
            @error('name')
            <span class="text-md text-red-500">Pavadinimas turi būti užpildytas</span>
            @enderror
        </div>

        <div class="w-full min-w-[200px] my-2">
            <label class="block mb-2 font-medium font-normal text-gray-600">
                Kaina
            </label>
            <input wire:model="price" class="w-full g-white/80 px-4 py-1.5  pl-4  rounded-lg bg-white/80 shadow-sm focus:shadow text-gray-600"  />
            @error('price')
            <span class="text-md text-red-500">Gali būti tik skaičiai</span>
            @enderror
        </div>

        <div class="my-4">
            <button wire:click="save"
                    class="w-full px-10 py-4 bg-emerald-700 rounded-2xl text-white/80 text-base font-semibold  leading-tight">
                Sukurti produktą
            </button>
        </div>
    </div>
</div>
