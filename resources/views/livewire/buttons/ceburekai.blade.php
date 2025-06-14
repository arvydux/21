<div wire:sortable="updateOrder" class="grid md:grid-cols-3 auto-rows-min gap-4 mt-4">
    @foreach(\App\Models\Ceburek::where('show', true)->orderBy('position')->get() as $productName)
        <div wire:sortable.handle  wire:sortable.item="{{ $productName->id }}" wire:key="{{ $loop->index }}">
        <flux:modal.trigger name="choose-toppings">
            <div  wire:click="getProductName('{{ $productName->name }}')" class="relative text-center  shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
            aspect-video overflow-hidden rounded-xl  dark:border-neutral-700">
                <div class="flex grid content-center flex-col gap-2 h-full text-white rounded-xl w-full bg-white/20 ">
{{--                    <button  wire:click="getProductName('{{ $productName->name }}')" >--}}
                    <button >
                        <div class="font-semibold h-full text-2xl tracking-tight">{{ $productName->name }}</div>
                    </button>
                </div>
            </div>
        </flux:modal.trigger>
        </div>
    @endforeach
        <flux:modal name="choose-toppings" class="w-full h-7/10 max-w-2xl  bg-white! rounded-xl shadow-xl blur-none">
            <form wire:submit.prevent="addOrder" >
                <div class="p-2">
                    <h3 class="text-md m-4 font-medium text-center text-gray-900 text-center ">Pasirinkti priedus:</h3>
                    <ul class="grid flex items-center w-full gap-1 md:grid-cols-5">
                        @foreach(\App\Models\Topping::where('show', true)->get()  as $topping)
                            <li class="grid flex-1 h-full">
                                <input type="checkbox" id="{{$topping->name}}" value="{{$topping->name}}" wire:model="toppings"
                                       class="hidden peer">
                                <label for="{{$topping->name}}"
                                       class="inline-flex items-center justify-between w-full p-2 bg-gray-900 text-gray-300 rounded-xl cursor-pointer  dark:border-gray-700 peer-checked:bg-emerald-700  peer-checked:text-white/80 text-base font-semibold  leading-tight  peer-checked:text-gray-600">
                                    <div class="flex-1  text-center text-xl">
                                        {{$topping->name}}
                                    </div>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>

            <div class="">
                <h3 class="m-6 text-md font-medium text-center text-gray-900">Kur valgys?</h3>
                <ul class="grid w-full gap-2 md:grid-cols-2">
                    <li>
                        <input type="radio" id="hosting-big" name="hosting" wire:model="takeaway" value="0" class="hidden peer">
                        <label for="hosting-big" class="inline-flex items-center justify-between w-full p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-emerald-600  peer-checked:text-emerald-700 hover:text-gray-600 hover:bg-gray-100 ">
                            <div class="block">
                                <div class="w-full text-sm font-semibold">Vietoje</div>
                            </div>
                            <svg class="w-5 h-5 ms-3 rtl:rotate-180 rotate-90" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </label>
                    </li>
                    <li>
                        <input type="radio" id="hosting-small" name="hosting" wire:model="takeaway" value="1" class="hidden peer" />
                        <label for="hosting-small" class="inline-flex items-center justify-between w-full p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-emerald-600 peer-checked:text-emerald-600 hover:text-gray-600 hover:bg-gray-100">
                            <div class="block">
                                <div class="w-full text-sm font-semibold">Išsinešimui</div>
                            </div>
                            <svg class="w-5 h-5 ms-3 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </label>
                    </li>
                </ul>
            </div>

                <div class="flex justify-center mt-auto w-full p-8 pb-0 gap-4">
                    <button type="submit" data-modal-toggle="choose-toppings" x-on:click="$flux.modals().close()"
                            class="rounded-xl cursor-pointer flex-1 bg-emerald-700 w-100 py-3 text-white inline-flex items-center  hover:bg-emerald-700 focus:ring-4 focus:outline-none  text-center ">

                        <div class="flex-1 text-xl font-semibold  leading-tight">
                            Pridėti ir uždaryti
                        </div>
                    </button>

                    <button type="submit" data-modal-toggle="choose-toppings"
                            class="rounded-xl cursor-pointer flex-1 bg-emerald-700 w-100 py-3  text-white inline-flex items-center  hover:bg-emerald-700 focus:ring-4 focus:outline-none  text-center">

                        <div class="flex-1 text-xl font-semibold  leading-tight">
                            Pridėti dar vieną
                        </div>
                    </button>
                </div>
            </form>
        </flux:modal>
</div>
