<div x-data x-init="initSwapSortable($el, $wire, 'updateOrder')" class="grid md:grid-cols-3 auto-rows-min gap-4 mt-4">
    @foreach(\App\Models\Ceburek::where('show', true)->orderBy('position')->get() as $productName)
        <div data-sortable-id="{{ $productName->id }}" wire:key="ceburek-{{ $productName->id }}">
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
        <flux:modal name="choose-toppings" class="w-full max-w-xl bg-white! rounded-xl shadow-2xl blur-none">
            <form wire:submit.prevent="addOrder" >
                <div class="bg-linear-to-b from-slate-50 to-slate-100 rounded-lg mx-2 p-2.5">
                    <h3 class="text-[10px] mb-2 font-bold text-center text-slate-500 tracking-widest uppercase">Pasirinkti priedus</h3>
                    <ul x-data x-init="initSwapSortable($el, $wire, 'updateToppingOrder')" class="grid w-full gap-1.5 md:grid-cols-5">
                        @foreach(\App\Models\Topping::where('show', true)->orderBy('position')->get()  as $topping)
                            <li data-sortable-id="{{ $topping->id }}" wire:key="topping-{{ $topping->id }}">
                                <input type="checkbox" id="{{$topping->name}}" value="{{$topping->name}}" wire:model="toppings"
                                       class="hidden peer">
                                <label for="{{$topping->name}}"
                                       class="flex items-center justify-center aspect-square w-full bg-linear-to-br from-slate-600 to-slate-800 text-white/90 rounded-lg cursor-pointer transition-all duration-200 shadow hover:from-slate-500 hover:to-slate-700 hover:shadow-md hover:-translate-y-0.5 peer-checked:from-emerald-500 peer-checked:to-teal-600 peer-checked:text-white peer-checked:shadow-md peer-checked:shadow-emerald-500/30 peer-checked:-translate-y-0.5 peer-checked:ring-2 peer-checked:ring-emerald-400/50 peer-checked:ring-offset-1 font-semibold leading-tight">
                                    <div class="text-center text-xs">
                                        {{$topping->name}}
                                    </div>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>

            <div class="mx-2 mt-2.5">
                <h3 class="mb-2 text-[10px] font-bold text-center text-slate-500 tracking-widest uppercase">Kur valgys?</h3>
                <ul class="grid w-full gap-2 md:grid-cols-2">
                    <li>
                        <input type="radio" id="hosting-big" name="hosting" wire:model="takeaway" value="0" class="hidden peer">
                        <label for="hosting-big" class="inline-flex items-center justify-center w-full py-2 px-3 text-slate-500 bg-white border border-gray-200 rounded-lg cursor-pointer transition-all duration-200 shadow-sm hover:shadow-md hover:border-gray-300 peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 peer-checked:shadow-md">
                            <svg class="w-4 h-4 mr-1.5 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.15c0 .415.336.75.75.75z" />
                            </svg>
                            <div class="text-sm font-bold">Vietoje</div>
                        </label>
                    </li>
                    <li>
                        <input type="radio" id="hosting-small" name="hosting" wire:model="takeaway" value="1" class="hidden peer" />
                        <label for="hosting-small" class="inline-flex items-center justify-center w-full py-2 px-3 text-slate-500 bg-white border border-gray-200 rounded-lg cursor-pointer transition-all duration-200 shadow-sm hover:shadow-md hover:border-gray-300 peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 peer-checked:shadow-md">
                            <svg class="w-4 h-4 mr-1.5 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            <div class="text-sm font-bold">Išsinešimui</div>
                        </label>
                    </li>
                </ul>
            </div>

                <div class="flex justify-center w-full px-2 pt-3 pb-1 gap-2">
                    <button type="submit" data-modal-toggle="choose-toppings" x-on:click="$flux.modals().close()"
                            class="rounded-lg cursor-pointer flex-1 bg-linear-to-r from-emerald-600 to-teal-600 py-2 text-white inline-flex items-center justify-center shadow-md shadow-emerald-500/20 hover:from-emerald-700 hover:to-teal-700 hover:shadow-lg focus:ring-2 focus:outline-none focus:ring-emerald-300 transition-all duration-200 active:scale-[0.98]">

                        <div class="flex-1 text-sm font-bold leading-tight text-center">
                            Pridėti ir uždaryti
                        </div>
                    </button>

                    <button type="submit" data-modal-toggle="choose-toppings"
                            class="rounded-lg cursor-pointer flex-1 bg-linear-to-r from-emerald-600 to-teal-600 py-2 text-white inline-flex items-center justify-center shadow-md shadow-emerald-500/20 hover:from-emerald-700 hover:to-teal-700 hover:shadow-lg focus:ring-2 focus:outline-none focus:ring-emerald-300 transition-all duration-200 active:scale-[0.98]">

                        <div class="flex-1 text-sm font-bold leading-tight text-center">
                            Pridėti dar vieną
                        </div>
                    </button>
                </div>
            </form>
        </flux:modal>
</div>
