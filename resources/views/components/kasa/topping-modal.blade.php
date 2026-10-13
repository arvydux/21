@props([
    'name',
    'idPrefix',
    'toppings',
    'toppingEditModal',
])
{{--
    Choose toppings and "Vietoje / Išsinešimui" for a product, then add it to the cart.
    Lives inside a button component whose x-data has koreguoti, chosenName and chosenPrice.
    The default slot goes above the toppings (e.g. the potato portion choice).
--}}
<flux:modal :name="$name" variant="bare"
            class="w-full max-w-3xl p-4 outline-none focus:outline-none backdrop:bg-emerald-950/80! backdrop:backdrop-blur-md">
    <form wire:submit.prevent="addOrder" data-topping-modal
          class="flex max-h-[calc(100dvh-2rem)] flex-col overflow-hidden rounded-3xl bg-linear-to-br from-emerald-800 to-teal-900 font-nunito text-white shadow-2xl shadow-black/50 ring-1 ring-white/20">
        <div class="flex items-center gap-3 border-b border-white/10 px-6 py-4">
            <div class="min-w-0 flex-1">
                <h2 class="kasa-title truncate" x-text="chosenName"></h2>
                <p class="kasa-meta text-amber-300" x-show="chosenPrice" x-text="chosenPrice"></p>
            </div>
            <button type="button" title="Koreguoti"
                    x-on:click="koreguoti = !koreguoti; window.koreguotiActive = koreguoti; window.dispatchEvent(new CustomEvent('koreguoti-changed', { detail: { active: koreguoti } }))"
                    x-bind:class="koreguoti ? 'bg-red-500 text-white ring-red-300 shadow-lg shadow-red-500/40' : 'bg-white/10 text-white/80 ring-white/15 hover:bg-white/20'"
                    class="flex size-12 shrink-0 touch-manipulation items-center justify-center rounded-xl ring-1 transition-all duration-200 active:scale-[0.95]">
                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4L18.768 9.232a2.5 2.5 0 00-3.536-3.536L4.464 16.464 4 20z"></path>
                </svg>
            </button>
            <button type="button" title="Uždaryti" x-on:click="$flux.modal(@js($name)).close()"
                    class="flex size-12 shrink-0 touch-manipulation items-center justify-center rounded-xl bg-white/10 text-white/80 ring-1 ring-white/15 transition-all duration-200 hover:bg-white/20 active:scale-[0.95]">
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="no-scrollbar flex min-h-0 flex-1 flex-col gap-5 overflow-y-auto overscroll-contain px-6 py-5">
            {{ $slot }}

            <section class="flex flex-col gap-2.5">
                <h3 class="kasa-meta uppercase tracking-widest">Priedai</h3>
                <ul x-init="initSwapSortable($el, $wire, 'updateToppingOrder')" class="grid grid-cols-3 gap-2.5 sm:grid-cols-4 md:grid-cols-5">
                    @foreach($toppings as $topping)
                        <li data-sortable-id="{{ $topping->id }}" wire:key="{{ $idPrefix }}-topping-{{ $topping->id }}"
                            data-attention="{{ $topping->attention ? '1' : '0' }}" class="group"
                            @click.capture="if (koreguoti) { $event.stopImmediatePropagation(); $event.preventDefault(); if ($el.dataset.attention === '1') { $wire.toggleToppingAttention({{ $topping->id }}); } else { editingToppingName = @js($topping->name); editingToppingId = {{ $topping->id }}; editingToppingLeft = ''; editingToppingAttention = false; $flux.modal(@js($toppingEditModal)).show(); } }">
                            <input type="checkbox" id="{{ $idPrefix }}-topping-{{ $topping->id }}" value="{{ $topping->name }}" wire:model.live="toppings"
                                   class="peer hidden">
                            <label for="{{ $idPrefix }}-topping-{{ $topping->id }}"
                                   @class([
                                       'relative flex min-h-20 cursor-pointer touch-manipulation select-none flex-col justify-between gap-1 rounded-2xl p-3 ring-1 transition-all duration-150 active:scale-[0.97]',
                                       'peer-checked:bg-white/25 peer-checked:ring-2 peer-checked:ring-amber-400 peer-checked:shadow-lg peer-checked:shadow-amber-500/20',
                                       'bg-red-500/70 ring-red-300/50' => $topping->attention,
                                       'bg-white/10 ring-white/15' => ! $topping->attention,
                                   ])>
                                <span class="absolute -right-1.5 -top-1.5 hidden size-6 items-center justify-center rounded-full bg-amber-400 text-emerald-950 shadow-md ring-2 ring-emerald-800 group-has-checked:flex">
                                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                                <span class="kasa-item line-clamp-2">{{ $topping->name }}</span>
                                <span class="flex items-baseline justify-between gap-2">
                                    @if((float) $topping->price > 0)
                                        <span class="kasa-meta text-amber-300">+{{ number_format((float) $topping->price, 2, ',', '') }} €</span>
                                    @endif
                                    @if($topping->left !== null)
                                        <span class="kasa-meta ml-auto text-white/80">
                                            <span x-show="editingToppingId === {{ $topping->id }} && editingToppingLeft !== ''" x-text="editingToppingLeft"></span><span x-show="!(editingToppingId === {{ $topping->id }} && editingToppingLeft !== '')">{{ $topping->left }}</span> vnt.
                                        </span>
                                    @else
                                        <span x-cloak x-show="editingToppingId === {{ $topping->id }} && editingToppingLeft !== ''" class="kasa-meta ml-auto text-white/80">
                                            <span x-text="editingToppingLeft"></span> vnt.
                                        </span>
                                    @endif
                                </span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </section>

            <section class="flex flex-col gap-2.5">
                <h3 class="kasa-meta uppercase tracking-widest">Kur valgys?</h3>
                <div class="grid grid-cols-2 gap-1 rounded-2xl bg-black/25 p-1">
                    @foreach(['0' => 'Vietoje', '1' => 'Išsinešimui'] as $value => $label)
                        <div>
                            <input type="radio" id="{{ $idPrefix }}-takeaway-{{ $value }}" name="{{ $idPrefix }}-takeaway" wire:model="takeaway" value="{{ $value }}" class="peer hidden">
                            <label for="{{ $idPrefix }}-takeaway-{{ $value }}"
                                   class="kasa-nav flex h-14 cursor-pointer touch-manipulation select-none items-center justify-center gap-2 rounded-xl text-white/65 transition-all duration-150 active:scale-[0.98]
                                          peer-checked:bg-amber-400 peer-checked:text-emerald-950 peer-checked:shadow-lg peer-checked:shadow-amber-500/20">
                                <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    @if($value === 1)
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72" />
                                    @endif
                                </svg>
                                {{ $label }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="grid grid-cols-2 gap-3 border-t border-white/10 px-6 py-4">
            <button type="submit"
                    class="flex h-14 touch-manipulation items-center justify-center gap-2 rounded-2xl bg-white/10 px-4 text-lg font-bold text-white ring-1 ring-white/15 transition-all duration-200 hover:bg-white/20 active:scale-[0.97]">
                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v12M6 12h12"></path>
                </svg>
                Pridėti dar vieną
            </button>
            <button type="submit" x-on:click="$flux.modals().close()"
                    class="flex h-14 touch-manipulation items-center justify-center rounded-2xl bg-white px-4 text-lg font-extrabold text-emerald-800 shadow-lg shadow-emerald-950/30 transition-all duration-200 hover:bg-emerald-50 active:scale-[0.97]">
                Pridėti ir uždaryti
            </button>
        </div>
    </form>
</flux:modal>
