<div class="grid md:grid-cols-3 auto-rows-min gap-4 mt-4">
    @foreach(\App\Models\Ceburek::all() as $productName)
        <flux:modal.trigger name="edit-profile">
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
    @endforeach
        <flux:modal name="edit-profile" class="w-full max-w-4xl  bg-white/95! rounded-2xl shadow-xl blur-none">
            <form wire:submit.prevent="addOrder" >
                <div class="p-8">
                    <h3 class="mb-5 text-lg font-medium text-center text-gray-900 text-center ">Pasirinkti priedus:</h3>
                    <ul class="grid w-full gap-6 md:grid-cols-3">
                        @foreach(\App\Models\Topping::all() as $topping)
                            <li>
                                <input type="checkbox" id="{{$topping->name}}" value="{{$topping->name}}" wire:model="toppings"
                                       class="hidden peer">
                                <label for="{{$topping->name}}"
                                       class="inline-flex items-center justify-between w-full p-15 bg-gray-900 text-gray-300 rounded-xl cursor-pointer  dark:border-gray-700 peer-checked:bg-emerald-700  peer-checked:text-white/80 text-base font-semibold  leading-tight  peer-checked:text-gray-600">
                                    <div class="flex-1  text-center text-2xl">
                                        {{$topping->name}}
                                    </div>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>




            <div class="p-8">
                <h3 class="mb-5 text-lg font-medium text-center text-gray-900 dark:text-white">Kur valgys?</h3>
                <ul class="grid w-full gap-6 md:grid-cols-2">
                    <li>
                        <input type="radio" id="hosting-big" name="hosting" wire:model="takeaway" value="1" class="hidden peer">
                        <label for="hosting-big" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-blue-500 peer-checked:border-emerald-600 dark:peer-checked:border-blue-600 peer-checked:text-emerald-700 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                            <div class="block">
                                <div class="w-full text-lg font-semibold">Vietoje</div>
                            </div>
                            <svg class="w-5 h-5 ms-3 rtl:rotate-180 rotate-90" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </label>
                    </li>
                    <li>
                        <input type="radio" id="hosting-small" name="hosting" wire:model="takeaway" value="0" class="hidden peer" />
                        <label for="hosting-small" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-blue-500 peer-checked:border-emerald-600 dark:peer-checked:border-blue-600 peer-checked:text-emerald-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                            <div class="block">
                                <div class="w-full text-lg font-semibold">Išsinešimui</div>
                            </div>
                            <svg class="w-5 h-5 ms-3 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </label>
                    </li>
                </ul>
            </div>

                <div class="flex justify-center mt-auto w-full p-8 pb-0 gap-6">
                    <button type="submit" data-modal-toggle="edit-profile" x-on:click="$flux.modals().close()"
                            class="rounded-xl cursor-pointer flex-1 bg-emerald-700 w-100 h-25 text-white inline-flex items-center  hover:bg-emerald-700 focus:ring-4 focus:outline-none  text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">

                        <div class="flex-1 text-2xl font-semibold  leading-tight">
                            Pridėti ir uždaryti
                        </div>
                    </button>

                    <button type="submit" data-modal-toggle="edit-profile"
                            class="rounded-xl cursor-pointer flex-1 bg-emerald-700 w-100 h-25 text-white inline-flex items-center  hover:bg-emerald-700 focus:ring-4 focus:outline-none  text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">

                        <div class="flex-1 text-2xl font-semibold  leading-tight">
                            Pridėti dar vieną
                        </div>
                    </button>
                </div>
            </form>
        </flux:modal>
</div>
